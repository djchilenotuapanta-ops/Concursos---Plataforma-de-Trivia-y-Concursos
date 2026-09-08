<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\Participation;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ParticipationController extends Controller
{
    /**
     * ==========================================================
     * REGISTRAR PARTICIPACIÓN / COMPRAR BOLETOS
     * ==========================================================
     * Reglas simples:
     * - El concurso debe estar activo y vigente
     * - Si es privado, debe tener código correcto (si aplica)
     * - Si tiene cupos, no puede pasarse del límite
     * - Si unique_by_cedula = 1, solo una vez por cédula (aunque use otro correo)
     * - Si max_tickets_per_user tiene valor, no puede superar ese límite
     *
     * Nota:
     * - Para empresas abiertas: unique_by_cedula = 0 y max_tickets_per_user = null (o alto)
     * - Para “solo una vez”: unique_by_cedula = 1 y max_tickets_per_user = 1
     */
    public function store(Request $request, $id)
    {
        // ----------------------------------------------------------
        // TRIVIA
        // ----------------------------------------------------------
        $contest = Contest::with('rule')->findOrFail($id);
        $user = auth()->user();

        $type = ($contest->type ?? null);
        if (!in_array($type, ['trivia'], true)) {
            return back()->with('error', 'Este concurso no está disponible.');
        }

        if (isset($contest->status) && $contest->status !== 'active') {
            return back()->with('error', 'Este concurso no está disponible en este momento.');
        }

        $now = now();
        if (!empty($contest->start_at) && $now->lt($contest->start_at)) {
            return back()->with('error', 'Este concurso aún no ha empezado.');
        }
        if (!empty($contest->end_at) && $now->gt($contest->end_at)) {
            if (($contest->status ?? null) === 'active') {
                $contest->status = 'ended';
                $contest->save();
                // Opcional: cerrar y publicar ganadores automáticamente
                app(\App\Services\ContestWinnerService::class)->close($contest, [
                    'winners_count' => 1,
                    'eligible_top_n' => 0,
                ]);
            }
            return back()->with('error', 'Este concurso ya terminó.');
        }

        if (!is_null($contest->rule?->max_participants)) {
            $alreadyIn = Participation::where('contest_id', $contest->id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$alreadyIn) {
                $current = Participation::where('contest_id', $contest->id)->count();
                if ($current >= (int) $contest->rule->max_participants) {
                    return back()->with('error', '⛔ Cupos agotados. Ya no se aceptan más participantes.');
                }
            }
        }

        if ($contest->rule?->unique_by_cedula) {
            if (!$user->cedula) {
                return back()->with('error', 'Debes registrar tu cédula para participar en esta trivia.');
            }

            $existsByCedula = Participation::where('contest_id', $contest->id)
                ->where('cedula_snapshot', $user->cedula)
                ->exists();

            if ($existsByCedula) {
                return back()->with('error', 'Ya existe una participación con esta cédula en esta trivia.');
            }
        }

        $ageGroup = $contest->rule?->age_group ?? 'all';
        $onlyAdults = (bool) ($contest->rule?->only_adults ?? false) || $ageGroup === 'adults';

        $needsBirthdate = $onlyAdults || $ageGroup === 'kids';

        if ($needsBirthdate && !$user->birthdate) {
            return back()->with('error', 'Para participar, primero registra tu fecha de nacimiento en tu perfil.');
        }

        $age = $user->age;

        if ($onlyAdults && (!is_int($age) || $age < 18)) {
            return back()->with('error', 'Esta trivia es solo para mayores de 18 años.');
        }

        if ($ageGroup === 'kids' && (!is_int($age) || $age >= 13)) {
            return back()->with('error', 'Esta trivia está dirigida a niños (menores de 13 años).');
        }

        $participation = Participation::where('contest_id', $contest->id)
            ->where('user_id', $user->id)
            ->first();

        if ($participation) {
            return back()->with('success', '✅ Ya estás inscrito en esta trivia.');
        }

        $participation = Participation::create([
            'contest_id' => $contest->id,
            'user_id' => $user->id,
            'tickets' => 1,
            'status' => 'active',
            'cedula_snapshot' => $user->cedula,
            'joined_at' => now(),
        ]);

        NotificationService::participationConfirmed($user, $contest);

        $participantsCount = Participation::where('contest_id', $contest->id)->count();
        if ($participantsCount === 1) {
            $company = User::find((int)$contest->company_id);
            if ($company) {
                NotificationService::send($company, '👤 Primer participante', '¡Ya tienes tu primer participante en "' . $contest->title . '"! (' . ($user->name ?: $user->email) . ')', route('company.contests.index'));
            }
        }
        return back()->with('success', '✅ Inscripción registrada correctamente.');
    }
}
