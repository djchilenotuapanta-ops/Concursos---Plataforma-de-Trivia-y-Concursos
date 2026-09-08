<?php

namespace App\Services;

use Illuminate\Http\RedirectResponse;

class FlashMessageService
{
    public static function success(string $message): RedirectResponse
    {
        return back()->with('success', $message);
    }

    public static function error(string $message): RedirectResponse
    {
        return back()->with('error', $message);
    }

    public static function warning(string $message): RedirectResponse
    {
        return back()->with('warning', $message);
    }

    public static function info(string $message): RedirectResponse
    {
        return back()->with('info', $message);
    }
}
