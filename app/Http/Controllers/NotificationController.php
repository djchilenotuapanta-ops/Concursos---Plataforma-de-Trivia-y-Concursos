<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user?->notifications ?? collect();

        if ($user) {
            $user->unreadNotifications?->markAsRead();
        }

        return view('notifications.index', compact('notifications'));
    }
}
