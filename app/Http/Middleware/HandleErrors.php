<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleErrors
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Recurso no encontrado',
                    'message' => 'El registro solicitado no existe'
                ], 404);
            }

            return redirect()->route('home')->with('error', 'El recurso solicitado no existe.');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'No autorizado',
                    'message' => 'No tienes permiso para realizar esta acción'
                ], 403);
            }

            return redirect()->route('home')->with('error', 'No tienes permiso para realizar esta acción.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        }
    }
}
