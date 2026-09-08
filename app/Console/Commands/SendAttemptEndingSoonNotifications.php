<?php

namespace App\Console\Commands;

use App\Models\Participation;
use App\Notifications\AttemptEndingSoonNotification;
use Illuminate\Console\Command;

class SendAttemptEndingSoonNotifications extends Command
{
    protected $signature = 'trivia:attempt-ending-soon {--seconds=60 : Avisar cuando falten N segundos}';

    protected $description = 'Notifica al usuario cuando su tiempo de intento está por terminar.';

    public function handle(): int
    {
        $window = (int) $this->option('seconds');
        if ($window < 10) {
            $window = 10;
        }

        $now = now();
        $limit = $now->copy()->addSeconds($window);

        $sent = 0;

        Participation::query()
            ->whereNotNull('attempt_expires_at')
            ->where('attempt_expires_at', '>', $now)
            ->where('attempt_expires_at', '<=', $limit)
            ->with(['user:id', 'contest:id,title'])
            ->select([
                'id',
                'user_id',
                'contest_id',
                'attempts_used',
                'attempt_expires_at',
                'ending_soon_notified_attempt',
            ])
            ->chunkById(500, function ($parts) use ($now, $window, &$sent) {
                foreach ($parts as $p) {
                    $u = $p->user;
                    $c = $p->contest;

                    if (!$u || !$c) {
                        continue;
                    }

                    $already = (int)($p->ending_soon_notified_attempt ?? 0) >= (int)($p->attempts_used ?? 0);
                    if ($already) {
                        continue;
                    }

                    $secondsLeft = max(1, $now->diffInSeconds($p->attempt_expires_at, false));
                    if ($secondsLeft > $window) {
                        continue;
                    }

                    $u->notify(new AttemptEndingSoonNotification(
                        (int) $c->id,
                        (string) ($c->title ?? 'Trivia'),
                        (int) $secondsLeft
                    ));

                    $p->ending_soon_notified_attempt = (int) $p->attempts_used;
                    $p->ending_soon_notified_at = now();
                    $p->save();

                    $sent++;
                }
            });

        $this->info('Notificaciones enviadas: ' . $sent);

        return self::SUCCESS;
    }
}
