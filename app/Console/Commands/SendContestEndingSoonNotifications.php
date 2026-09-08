<?php

namespace App\Console\Commands;

use App\Models\Contest;
use App\Models\Participation;
use App\Notifications\ContestEndingSoonNotification;
use Illuminate\Console\Command;

class SendContestEndingSoonNotifications extends Command
{
    protected $signature = 'contests:ending-soon {--hours=6 : Ventana en horas para avisar (por defecto 6)}';

    protected $description = 'Notifica a los usuarios cuando un concurso está por finalizar.';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        if ($hours < 1) {
            $hours = 1;
        }

        $now = now();
        $limit = $now->copy()->addHours($hours);

        $contests = Contest::query()
            ->where('type','trivia')
            ->where('status', 'active')
            ->whereNotNull('end_at')
            ->where('end_at', '>', $now)
            ->where('end_at', '<=', $limit)
            ->get(['id', 'title', 'end_at']);

        $sent = 0;

        foreach ($contests as $contest) {
            $hoursLeft = max(1, (int) ceil($now->diffInMinutes($contest->end_at) / 60));

            Participation::query()
                ->where('contest_id', $contest->id)
                ->whereNotNull('joined_at')
                ->with('user:id')
                ->select('id', 'user_id')
                ->chunkById(500, function ($parts) use ($contest, $hoursLeft, &$sent) {
                    foreach ($parts as $p) {
                        $u = $p->user;
                        if (!$u) continue;

                        $already = $u->notifications()
                            ->where('created_at', '>=', now()->subDay())
                            ->where('data->kind', 'ending_soon')
                            ->where('data->contest_id', (int) $contest->id)
                            ->exists();

                        if ($already) {
                            continue;
                        }

                        $u->notify(new ContestEndingSoonNotification($contest, $hoursLeft));
                        $sent++;
                    }
                });
        }

        $this->info('Notificaciones enviadas: ' . $sent);

        return self::SUCCESS;
    }
}
