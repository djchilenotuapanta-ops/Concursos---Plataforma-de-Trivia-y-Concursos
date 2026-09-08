<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    private function normalizeRole(?string $role): string
    {
        $r = strtolower(trim((string) $role));

        $r = preg_replace('/\s+/', '', $r);

        return match ($r) {
            'administrator', 'administrador', 'admin' => 'admin',
            'empresa', 'company' => 'company',
            'moderador', 'moderator' => 'moderator',
            'participante', 'participantes', 'usuario', 'user' => 'user',
            default => strtolower(trim((string) $role)),
        };
    }

    private function normalizeAllowed(string $role): string
    {
        return $this->normalizeRole($role);
    }

    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $allowed = [];
        foreach ($roles as $r) {
            foreach (preg_split('/[|,]/', (string) $r) as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $allowed[] = $this->normalizeAllowed($part);
                }
            }
        }

        $current = $this->normalizeRole($user->role);

        if (!in_array($current, $allowed, true)) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'No tienes permiso para esa sección. Te redirigimos a tu panel.');
        }

        return $next($request);
    }
}
