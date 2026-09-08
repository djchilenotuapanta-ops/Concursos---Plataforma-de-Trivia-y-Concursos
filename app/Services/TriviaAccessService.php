<?php

namespace App\Services;

use App\Exceptions\ContestAccessDeniedException;
use App\Exceptions\TriviaAttemptsExhaustedException;
use App\Exceptions\TriviaTimeExpiredException;
use App\Models\Contest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class TriviaAccessService
{
    public static function validateContestAccess(Contest $contest): void
    {
        if ($contest->status !== 'active') {
            throw new ContestAccessDeniedException('Este concurso no está disponible.');
        }

        if (now()->lessThan($contest->start_at)) {
            throw new ContestAccessDeniedException('Este concurso aún no ha empezado.');
        }

        if (now()->greaterThan($contest->end_at)) {
            throw new ContestAccessDeniedException('Este concurso ya terminó.');
        }
    }

    public static function validateUserAge(Contest $contest, User $user): void
    {
        $age = $user->age;

        if (!$age) {
            return;
        }

        $audience = $contest->audience ?? 'all';

        if ($audience === 'kid' && $age >= 13) {
            throw new ContestAccessDeniedException('Esta trivia está dirigida a niños menores de 13 años.');
        }

        if ($audience === 'adult' && $age < 18) {
            throw new ContestAccessDeniedException('Este concurso es solo para mayores de 18 años.');
        }
    }

    public static function validateCapacity(Contest $contest): void
    {
        $rule = $contest->rule;

        if (!$rule) {
            return;
        }

        $maxParticipants = (int)($rule->max_participants ?? 0);

        if ($maxParticipants > 0) {
            $currentCount = $contest->participants()->count();

            if ($currentCount >= $maxParticipants) {
                throw new ContestAccessDeniedException('⛔ Cupos agotados. Ya no se aceptan más participantes.');
            }
        }
    }

    public static function validateAttempts($progress, $maxAttempts): void
    {
        if ($progress && (int)$progress->attempts_used >= $maxAttempts) {
            throw new TriviaAttemptsExhaustedException();
        }
    }

    public static function validateTimeLimit($progress, $timeLimit): void
    {
        if (!$progress || !$progress->started_at) {
            return;
        }

        $elapsed = now()->diffInSeconds($progress->started_at);

        if ($elapsed > $timeLimit) {
            throw new TriviaTimeExpiredException();
        }
    }
}
