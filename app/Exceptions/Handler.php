<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\ErrorLogService;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if ($e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
                return;
            }

            ErrorLogService::logError($e, 'Global exception handler');
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Recurso no encontrado.',
                    'message' => 'El registro solicitado no existe.',
                ], 404);
            }

            return redirect()->route('home')->with('error', 'El recurso solicitado no existe.');
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Página no encontrada.',
                    'message' => 'La ruta solicitada no existe.',
                ], 404);
            }

            return response()->view('errors.404', [], 404);
        });

        $this->renderable(function (ContestAccessDeniedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Acceso denegado.',
                    'message' => $e->getMessage(),
                ], 403);
            }

            return redirect()->route('home')->with('error', $e->getMessage());
        });

        $this->renderable(function (TriviaTimeExpiredException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Tiempo expirado.',
                    'message' => $e->getMessage(),
                ], 403);
            }

            return redirect()->route('user.dashboard')->with('error', $e->getMessage());
        });

        $this->renderable(function (TriviaAttemptsExhaustedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Intentos agotados.',
                    'message' => $e->getMessage(),
                ], 403);
            }

            return redirect()->route('user.dashboard')->with('error', $e->getMessage());
        });

        $this->renderable(function (HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Acceso denegado.',
                        'message' => $e->getMessage() ?: 'No tienes permiso para acceder a este recurso.',
                    ], 403);
                }

                $user = auth()->user();
                $role = $user ? strtolower($user->role) : null;

                $dashboardRoute = match($role) {
                    'admin', 'administrator', 'administrador' => 'admin.dashboard',
                    'company', 'empresa' => 'company.dashboard',
                    'moderator', 'moderador' => 'moderator.dashboard',
                    'user' => 'user.dashboard',
                    default => 'home'
                };

                return redirect()->route($dashboardRoute)->with('error', $e->getMessage() ?: 'Acceso denegado.');
            }

            if ($e->getStatusCode() === 500) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Error del servidor.',
                        'message' => 'Ha ocurrido un error inesperado.',
                    ], 500);
                }

                return response()->view('errors.500', [], 500);
            }
        });
    }
}
