<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;

class UserService
{
    public static function normalizeRole(?string $role): string
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

    public static function activateSubscriptionForOneMonth(User $user): void
    {
        $user->subscription_start = Carbon::now();
        $user->subscription_end = Carbon::now()->addMonth();
        $user->save();
    }

    public static function getAdmins()
    {
        return User::query()->whereIn('role', ['admin', 'administrator', 'administrador'])->get();
    }
}
