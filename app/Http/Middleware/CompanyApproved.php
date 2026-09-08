<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompanyApproved
{
    private function normalizeRole(?string $role): string
    {
        $r = strtolower(trim((string) $role));

        return match ($r) {
            'administrator', 'administrador', 'admin' => 'admin',
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

        if ($role !== 'company') {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Esta sección es solo para empresas.');
        }

        if ((int) ($user->active ?? 1) !== 1) {
            return redirect()
                ->route('company.dashboard')
                ->with('error', 'La empresa está inactivada.');
        }

        if ((int) ($user->approved ?? 0) !== 1) {
            return redirect()
                ->route('company.dashboard')
                ->with('error', 'Tu empresa aún no está aprobada. Espera la aprobación del administrador.');
        }

        return $next($request);
    }
}
