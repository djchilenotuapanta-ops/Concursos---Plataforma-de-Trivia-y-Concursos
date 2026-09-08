<?php

namespace App\Services;

use App\Models\Contest;
use App\Models\Participation;
use App\Models\Prize;
use App\Models\User;
use App\Notifications\DbNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ContestWinnerService
{
    public function close(Contest $contest, array $options = []): array
    {
        $force = (bool)($options['force'] ?? false);

        if (($contest->type ?? null) !== 'trivia') {
            return [
                'ok' => false,
                'message' => 'Este servicio solo publica ganadores de concursos tipo trivia.',
                'winners' => [],
                'draw_winner_user_id' => null,
            ];
        }

        if ($contest->status === 'ended' && !$force) {
            return [
                'ok' => false,
                'message' => 'El concurso ya fue cerrado.',
                'winners' => [],
                'draw_winner_user_id' => null,
            ];
        }

        if (!$contest->end_at || Carbon::parse($contest->end_at)->isFuture()) {
            return [
                'ok' => false,
                'message' => '⚠️ El concurso aún no finaliza.',
                'winners' => [],
                'draw_winner_user_id' => null,
            ];
        }

        return DB::transaction(function () use ($contest, $options) {
            // Fuente de verdad: si last_finished_at no es NULL, el usuario terminó su intento.
            // (status puede no estar seteado en algunos flujos.)
            $participations = Participation::where('contest_id', $contest->id)
                ->whereNotNull('last_finished_at')
                ->get();

            // Excluir cuentas internas (admin/empresa/moderador) de la competencia.
            $participations = $participations->filter(function ($p) {
                $user = User::find((int) $p->user_id);
                if (!$user) return false;
                return !in_array($user->role, ['admin', 'administrator', 'administrador', 'company', 'moderator', 'moderador'], true);
            })->values();

            if ($participations->isEmpty()) {
                $contest->update([
                    'status' => 'ended',
                    'winner_published_at' => Carbon::now(),
                ]);

                $company = User::find((int)$contest->company_id);
                if ($company) {
                    $company->notify(new DbNotification([
                        'title' => '😕 Trivia sin participantes',
                        'message' => 'La trivia "' . $contest->title . '" finalizó sin participantes finalizados.',
                        'url' => route('company.contests.index'),
                    ]));
                }

                User::adminsQuery()->get()->each(function ($a) use ($contest, $company) {
                    $a->notify(new DbNotification([
                        'title' => '😕 Trivia finalizó sin participantes',
                        'message' => 'La trivia "' . $contest->title . '" de la empresa "' . (($company->company_name ?? $company->name) ?? ('company_id=' . (int)$contest->company_id)) . '" finalizó sin participantes finalizados.',
                        'url' => route('admin.contests_adv.index'),
                    ]));
                });

                return [
                    'ok' => false,
                    'message' => 'No hay participantes finalizados.',
                    'winners' => [],
                    'draw_winner_user_id' => null,
                ];
            }

            $winnerMethod = ($contest->winner_method ?? 'ranking');
            // Por defecto, aplicar reglas guardadas en la BD.
            // options funciona como override (por ejemplo, desde un formulario de admin).
            $winnersCount = (int)($contest->rules?->winners_count ?? ($options['winners_count'] ?? 1));
            $eligibleTopN = (int)($contest->rules?->eligible_top_n ?? ($options['eligible_top_n'] ?? 0));
            $winnersCount = max(1, min(100, $winnersCount));
            $eligibleTopN = max(0, min(500, $eligibleTopN));

            $bestByUser = $this->bestByUser($participations);
            // Solo usuarios con al menos una respuesta correcta (si quieres permitir 0, elimina este filtro)
            $bestByUser = $bestByUser->filter(fn($row) => (int)($row['correct'] ?? 0) > 0)->values();
            $sorted = $this->sortBest($bestByUser);

            $topWinners = collect();
            $eligibleCount = null; // Solo aplica al método sorteo

            if ($winnerMethod === 'lottery') {
                // Sorteo: participantes elegibles (por defecto: todos los que tengan al menos 1 acierto).
                $eligibleUsers = $sorted->pluck('user_id')->unique()->values();
                if ($eligibleTopN > 0) {
                    $eligibleUsers = $eligibleUsers->take($eligibleTopN);
                }

                $eligibleCount = $eligibleUsers->count();

                if ($eligibleUsers->isEmpty()) {
                    return [
                        'ok' => false,
                        'message' => 'No hay participantes elegibles para el sorteo.',
                        'winners' => [],
                        'draw_winner_user_id' => null,
                    ];
                }

                $winnersCount = min($winnersCount, $eligibleUsers->count());
                $picked = $eligibleUsers->shuffle()->take($winnersCount)->values();
                $topWinners = $picked->map(function ($uid, $i) {
                    return [
                        'position' => $i + 1,
                        'user_id' => (int)$uid,
                        'correct' => null,
                        'wrong' => null,
                        'seconds' => null,
                    ];
                });
            } else {
                $topWinners = $sorted->take($winnersCount)->values()->map(function ($row, $i) {
                    return [
                        'position' => $i + 1,
                        'user_id' => (int)$row['user_id'],
                        'correct' => (int)$row['correct'],
                        'wrong' => (int)$row['wrong'],
                        'seconds' => $row['seconds'] === null ? null : (int)$row['seconds'],
                    ];
                });
            }

            if ($topWinners->isEmpty() || (int)($topWinners->first()['user_id'] ?? 0) < 1) {
                return [
                    'ok' => false,
                    'message' => 'No se pudo determinar el/los ganadores.',
                    'winners' => [],
                    'draw_winner_user_id' => null,
                ];
            }

            $contest->update([
                'status' => 'ended',
            ]);

            $rankingPrizes = Prize::where('contest_id', $contest->id)
                ->where('kind', 'ranking')
                ->orderBy('position')
                ->get();
            $lotteryPrizes = Prize::where('contest_id', $contest->id)
                ->where('kind', 'lottery')
                ->orderBy('position')
                ->get();

            foreach ($topWinners as $w) {
                $pos = (int)($w['position'] ?? 1);
                $uid = (int)($w['user_id'] ?? 0);
                if ($uid < 1) continue;

                $prize = null;
                if ($winnerMethod === 'lottery') {
                    $prize = $lotteryPrizes->firstWhere('position', $pos);
                    if (!$prize) {
                        $base = $lotteryPrizes->first() ?: $rankingPrizes->first();
                        $prize = Prize::create([
                            'contest_id' => $contest->id,
                            'kind' => 'lottery',
                            'name' => $base->name ?? 'Premio sorteo',
                            'quantity' => (int)($base->quantity ?? 1),
                            'image_path' => $base->image_path ?? null,
                            'position' => $pos,
                        ]);
                        $lotteryPrizes->push($prize);
                    }
                } else {
                    $prize = $rankingPrizes->firstWhere('position', $pos);
                    if (!$prize) {
                        $base = $rankingPrizes->first();
                        $prize = Prize::create([
                            'contest_id' => $contest->id,
                            'kind' => 'ranking',
                            'name' => $base->name ?? 'Premio ranking',
                            'quantity' => (int)($base->quantity ?? 1),
                            'image_path' => $base->image_path ?? null,
                            'position' => $pos,
                        ]);
                        $rankingPrizes->push($prize);
                    }
                }

                $prize->update([
                    'winner_user_id' => $uid,
                    'won_at' => Carbon::now(),
                ]);
            }

            $drawWinnerId = null;
            $drawPrize = Prize::where('contest_id', $contest->id)->where('kind', 'draw')->first();
            if ((bool)($contest->draw_enabled ?? false) && $drawPrize) {
                $topN = max(1, (int)($contest->draw_top_n ?? 10));
                $eligible = $sorted->take($topN)->pluck('user_id')->values();

                if ($eligible->count() > 1) {
                    $excludeIds = $topWinners->pluck('user_id')->map(fn($v) => (int)$v)->all();
                    $filtered = $eligible->filter(fn($id) => !in_array((int)$id, $excludeIds, true))->values();
                    if ($filtered->isNotEmpty()) {
                        $eligible = $filtered;
                    }
                }

                if ($eligible->isNotEmpty()) {
                    $drawWinnerId = (int)$eligible->random();
                    $drawPrize->update([
                        'winner_user_id' => $drawWinnerId,
                        'won_at' => Carbon::now(),
                    ]);
                }
            }

            $posByUser = [];
            foreach ($topWinners as $w) {
                $posByUser[(int)($w['user_id'] ?? 0)] = (int)($w['position'] ?? 1);
            }

            $winnerNames = [];
            foreach ($topWinners as $w) {
                $uid = (int)($w['user_id'] ?? 0);
                $pos = (int)($w['position'] ?? 1);
                $user = User::find($uid);
                if ($user) {
                    $winnerNames[] = $pos . '. ' . ($user->name ?: $user->email);
                }
            }
            $winnersList = implode(', ', $winnerNames);

            // Texto humano del método de elección (para notificaciones)
            $methodText = $winnerMethod === 'lottery'
                ? ('Método: Sorteo aleatorio' . ($eligibleTopN > 0 ? (" (solo Top {$eligibleTopN})") : '') . ($eligibleCount !== null ? (" | Elegibles: {$eligibleCount}") : ''))
                : 'Método: Ranking (más aciertos; si empatan, menor tiempo)';



            $company = User::find((int)$contest->company_id);
            $topSummary = $sorted->map(function ($row, $i) {
                $user = User::find((int)$row['user_id']);
                $name = $user ? $user->name : ('user_id=' . $row['user_id']);
                return sprintf(
                    "%d) %s - %d aciertos - %ss",
                    $i + 1,
                    $name,
                    (int)$row['correct'],
                    $row['seconds'] === null ? '-' : (int)$row['seconds']
                );
            })->implode("\n");

            if ($company) {
                $company->notify(new DbNotification([
                    'title' => '🏁 Concurso finalizado',
                    'message' => "La trivia \"{$contest->title}\" finalizó.\n\nRanking completo:\n" . $topSummary,
                    'url' => route('company.contests.index'),
                ]));
            }

            User::adminsQuery()->get()->each(function ($a) use ($contest, $company, $topSummary) {
                $a->notify(new DbNotification([
                    'title' => '🏁 Concurso finalizado',
                    'message' => 'Ranking completo de "' . $contest->title . '"' . ($company ? ' (Empresa: ' . ($company->company_name ?? $company->name) . ')' : '') . ":\n" . $topSummary,
                    'url' => route('admin.contests_adv.index'),
                ]));
            });

            User::where('role', 'moderator')->get()->each(function ($m) use ($contest, $company, $topSummary) {
                $m->notify(new DbNotification([
                    'title' => '🏁 Concurso finalizado',
                    'message' => 'Ranking completo de "' . $contest->title . '"' . ($company ? ' (Empresa: ' . ($company->company_name ?? $company->name) . ')' : '') . ":\n" . $topSummary,
                    'url' => route('admin.contests_adv.index'),
                ]));
            });

            return [
                'ok' => true,
                'message' => '✅ Concurso cerrado y ranking enviado a admin, empresa y moderador.',
                'winners' => $topWinners->values()->all(),
                'draw_winner_user_id' => $drawWinnerId,
            ];
        });
    }

    public function publishWinners(Contest $contest): array
    {
        if ($contest->winner_published_at) {
            return [
                'ok' => false,
                'message' => 'Los ganadores ya fueron publicados.',
            ];
        }
        $participations = Participation::where('contest_id', $contest->id)
            ->whereNotNull('last_finished_at')
            ->get();
        $bestByUser = $this->bestByUser($participations);
        $bestByUser = $bestByUser->filter(fn($row) => (int)($row['correct'] ?? 0) > 0)->values();
        $sorted = $bestByUser->sort(function($a, $b) {
            if ((int)$b['correct'] !== (int)$a['correct']) {
                return (int)$b['correct'] <=> (int)$a['correct'];
            }
            return (int)$a['seconds'] <=> (int)$b['seconds'];
        })->values();
        $winnersCount = (int)($contest->rules?->winners_count ?? 1);
        $topWinners = $sorted->take($winnersCount)->values()->map(function ($row, $i) {
            return [
                'position' => $i + 1,
                'user_id' => (int)$row['user_id'],
                'correct' => (int)$row['correct'],
                'wrong' => (int)$row['wrong'],
                'seconds' => $row['seconds'] === null ? null : (int)$row['seconds'],
            ];
        });

        // Limpiar winner_type para todos los participantes de este concurso
        \App\Models\Participation::where('contest_id', $contest->id)
            ->update(['winner_type' => null]);
        // Marcar como "merit" a los ganadores principales
        foreach ($topWinners as $w) {
            \App\Models\Participation::where('contest_id', $contest->id)
                ->where('user_id', $w['user_id'])
                ->update(['winner_type' => 'merit']);
        }
        $posByUser = [];
        foreach ($topWinners as $w) {
            $posByUser[(int)($w['user_id'] ?? 0)] = (int)($w['position'] ?? 1);
        }
        $winnerNames = [];
        foreach ($topWinners as $w) {
            $uid = (int)($w['user_id'] ?? 0);
            $pos = (int)($w['position'] ?? 1);
            $user = User::find($uid);
            if ($user) {
                $winnerNames[] = $pos . '. ' . ($user->name ?: $user->email);
            }
        }
        $winnersList = implode(', ', $winnerNames);
        $methodText = 'Método: Ranking (más aciertos; si empatan, menor tiempo)';
        $allUsers = $participations->pluck('user_id')->unique();
        // Notificar solo a ganadores y a quienes participaron
        foreach ($allUsers as $uid) {
            $uid = (int)$uid;
            $isWinner = isset($posByUser[$uid]);
            $user = User::find($uid);
            if (!$user) continue;
            if ($isWinner) {
                $user->notify(new DbNotification([
                    'title' => 'Ganador',
                    'message' => '¡Ganaste el concurso ' . $contest->title . '!',
                    'url' => route('user.contests.list'),
                ]));
            } else if ($participations->where('user_id', $uid)->count() > 0) {
                $ganador = $topWinners->first();
                $ganadorNombre = $ganador ? (User::find($ganador['user_id'])->name ?? '—') : '—';
                $user->notify(new DbNotification([
                    'title' => 'Concurso finalizado',
                    'message' => 'El concurso ' . $contest->title . ' ha finalizado. Ganador: ' . $ganadorNombre,
                    'url' => route('user.contests.list'),
                ]));
            }
        }
        // Notificar a admin y empresa con resumen
        $company = User::find((int)$contest->company_id);
        if ($company) {
            $company->notify(new DbNotification([
                'title' => '🏁 Ganadores publicados',
                'message' => 'Ganadores de "' . $contest->title . '": ' . $winnersList,
                'url' => route('company.contests.index'),
            ]));
        }
        User::adminsQuery()->get()->each(function ($a) use ($contest, $winnersList) {
            $a->notify(new DbNotification([
                'title' => '🏁 Ganadores publicados',
                'message' => 'Ganadores de "' . $contest->title . '": ' . $winnersList,
                'url' => route('admin.contests_adv.index'),
            ]));
        });
        $contest->update([
            'winner_published_at' => Carbon::now(),
        ]);
        return [
            'ok' => true,
            'message' => 'Ganadores publicados y notificados a los usuarios.',
        ];
    }

    private function bestByUser(Collection $participations): Collection
    {
        return $participations
            ->groupBy('user_id')
            ->map(function ($rows) {
                $bestCorrect = (int)$rows->max('last_correct');
                $bestRows = $rows->where('last_correct', $bestCorrect);

                $bestSeconds = $bestRows->min('last_duration_seconds');
                if ($bestSeconds === null) {
                    $bestSeconds = null;
                }

                $bestRow = $bestRows->sortBy(function ($r) {
                    return $r->last_duration_seconds ?? PHP_INT_MAX;
                })->first();

                return [
                    'user_id' => (int)$rows->first()->user_id,
                    'correct' => $bestCorrect,
                    'wrong' => (int)($bestRow->last_wrong ?? 0),
                    'seconds' => $bestRow->last_duration_seconds === null ? null : (int)$bestRow->last_duration_seconds,
                ];
            })
            ->values();
    }

    private function sortBest(Collection $bestByUser): Collection
    {
        return $bestByUser->sort(function ($a, $b) {
            if ((int)$a['correct'] === (int)$b['correct']) {
                $sa = $a['seconds'];
                $sb = $b['seconds'];
                if ($sa === null && $sb === null) return 0;
                if ($sa === null) return 1;
                if ($sb === null) return -1;
                return $sa <=> $sb;
            }
            return (int)$b['correct'] <=> (int)$a['correct'];
        })->values();
    }
}
