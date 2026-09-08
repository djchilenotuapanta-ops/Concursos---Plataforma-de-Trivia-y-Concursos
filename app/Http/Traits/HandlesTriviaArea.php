<?php

namespace App\Http\Traits;

use App\Models\Contest;
use App\Exceptions\ContestAccessDeniedException;
use Illuminate\Support\Str;

trait HandlesTriviaArea
{
    protected function normalizeRole(?string $role): string
    {
        $r = strtolower(trim((string) $role));
        $r = preg_replace('/\s+/', '', $r);

        return match ($r) {
            'administrator', 'administrador', 'admin' => 'admin',
            'empresa', 'company' => 'company',
            'moderador', 'moderator' => 'moderator',
            'participante', 'participantes', 'usuario', 'user' => 'user',
            default => $r,
        };
    }

    protected function guardTriviaOwner(Contest $contest): Contest
    {
        $user = auth()->user();

        if (!$user) {
            throw new ContestAccessDeniedException('Debes iniciar sesión para acceder.');
        }

        if (($contest->type ?? null) !== 'trivia') {
            abort(404, 'Este concurso no es una trivia.');
        }

        $role = $this->normalizeRole($user->role);

        if (in_array($role, ['admin', 'moderator'], true)) {
            return $contest;
        }

        if ($role === 'company') {
            if ((int) $contest->company_id !== (int) $user->id) {
                throw new ContestAccessDeniedException('No tienes permiso para acceder a esta trivia.');
            }
            return $contest;
        }

        throw new ContestAccessDeniedException('Acceso no autorizado.');
    }

    protected function viewBase(string $area): string
    {
        return match ($area) {
            'admin' => 'admin',
            'moderator' => 'moderator',
            default => 'company',
        };
    }

    protected function routeBase(string $area): string
    {
        return match ($area) {
            'admin' => 'admin.',
            'moderator' => 'moderator.',
            default => 'company.',
        };
    }
}
