<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyActive
{
    private function normalizeRole(?string $role): string
    {
        $r = strtolower(trim((string) $role));

        return match ($r) {
            'administrator', 'administrador', 'admin' => 'admin',
            'empresa', 'company' => 'company',
            'moderador', 'moderator' => 'moderator',
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

        if ($role === 'company' && (int) ($user->active ?? 1) !== 1) {
            Auth::logout();

            return redirect()
                ->route('login')
                ->with('error', 'Tu empresa está inactivada. Contacta al administrador.');
        }

        return $next($request);
    }
}
