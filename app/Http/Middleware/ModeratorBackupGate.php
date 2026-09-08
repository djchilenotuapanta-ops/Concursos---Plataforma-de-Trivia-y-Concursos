<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\User;

class ModeratorBackupGate
{
    private function normalizeRole(?string $role): string
    {
        $r = strtolower(trim((string) $role));
        $r = preg_replace('/\s+/', '', $r);

        return match ($r) {
            'administrator', 'administrador', 'admin' => 'admin',
            'moderador', 'moderator' => 'moderator',
            'empresa', 'company' => 'company',
            'participante', 'participantes', 'usuario', 'user' => 'user',
            default => $r,
        };
    }

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $role = $this->normalizeRole($user->role);

        if ($role === 'admin') {
            return $next($request);
        }

        if ($role !== 'moderator') {
            return redirect()
                ->route('home')
                ->with('error', 'No tienes permiso para esa sección.');
        }

        $backupOn = (string) SystemSetting::getValue('moderator_backup_mode', '0') === '1';

        $adminActive = User::query()
            ->where('active', 1)
            ->whereRaw("LOWER(TRIM(role)) IN ('admin','administrator','administrador')")
            ->exists();

        if (!$adminActive || $backupOn) {
            return $next($request);
        }

        return redirect()
            ->route('home')
            ->with('error', 'Acceso moderador deshabilitado: hay un administrador activo y el modo respaldo está OFF.');
    }
}
