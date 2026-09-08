<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;

class AdminContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20);

        $unreadCount = ContactMessage::whereNull('read_at')->count();

        return view('admin.contact_messages.index', compact('messages', 'unreadCount'));
    }

    public function show(ContactMessage $contactMessage)
    {
        if (!$contactMessage->read_at) {
            $contactMessage->update(['read_at' => now()]);
        }

        return view('admin.contact_messages.show', compact('contactMessage'));
    }

    public function markRead(ContactMessage $contactMessage)
    {
        $contactMessage->update(['read_at' => now()]);

        return back()->with('success', 'Mensaje marcado como leído.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact_messages.index')
            ->with('deleted', 'Mensaje eliminado.');
    }
}
