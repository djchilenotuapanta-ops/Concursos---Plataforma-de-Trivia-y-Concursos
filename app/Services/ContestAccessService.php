<?php

namespace App\Services;

use App\Models\Contest;
use App\Models\User;
use App\Exceptions\ContestAccessDeniedException;

class ContestAccessService
{
    public static function validateOwnership(Contest $contest, User $user, string $area): void
    {
        if ($area === 'company') {
            if ((int)$contest->company_id !== (int)$user->id) {
                throw new ContestAccessDeniedException('No tienes permiso para acceder a este concurso.');
            }
        }
    }

    public static function canPublish(Contest $contest, User $user, string $area): bool
    {
        if ($area === 'company') {
            return (int)$contest->company_id === (int)$user->id;
        }

        $role = strtolower(trim((string)$user->role));

        return in_array($role, ['admin', 'administrator', 'administrador', 'moderator', 'moderador'], true);
    }

    public static function canManageWinners(Contest $contest, User $user, string $area): bool
    {
        if ($area !== 'admin') {
            return false;
        }

        $role = strtolower(trim((string)$user->role));

        return in_array($role, ['admin', 'administrator', 'administrador'], true);
    }
}
