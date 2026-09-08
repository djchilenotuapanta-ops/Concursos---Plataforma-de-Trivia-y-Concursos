<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Throwable;

class ErrorLogService
{
    public static function logError(Throwable $e, ?string $context = null): void
    {
        Log::error('Error: ' . $e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'context' => $context,
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_id' => auth()->id(),
        ]);
    }

    public static function logWarning(string $message, array $context = []): void
    {
        Log::warning($message, array_merge($context, [
            'url' => request()->fullUrl(),
            'user_id' => auth()->id(),
        ]));
    }

    public static function logInfo(string $message, array $context = []): void
    {
        Log::info($message, array_merge($context, [
            'url' => request()->fullUrl(),
            'user_id' => auth()->id(),
        ]));
    }
}
