<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyProfileCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $u = Auth::user();

        if (!$u) {
            return $next($request);
        }

        if ($u->role !== 'company') {
            return $next($request);
        }

        $incomplete = empty($u->company_name) || empty($u->razon_social) || empty($u->ruc);

        if ($incomplete) {
            return redirect()
                ->route('company.profile.edit')
                ->with('warning', '⚠️ Completa tu perfil de empresa (Nombre comercial, Razón social y RUC) para continuar.');
        }

        return $next($request);
    }
}
