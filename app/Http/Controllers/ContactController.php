<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\DbNotification;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'email', 'max:190'],
            'phone'   => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['required', 'string', 'min:5', 'max:3000'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo debe ser válido.',
            'message.required' => 'El mensaje es obligatorio.',
        ]);

        $msg = ContactMessage::create([
            ...$data,

            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new DbNotification([
                'title'   => 'Nuevo mensaje de contacto',
                'message' => ($msg->name ?? 'Alguien') . ' envió un mensaje desde la landing.',
                'url'     => url('/admin/contact-messages/' . $msg->id),
                'meta'    => [
                    'contact_message_id' => $msg->id,
                    'email' => $msg->email,
                    'subject' => $msg->subject,
                ],
            ]));
        }


        return back()->with('success', 'Mensaje enviado ✅ Un administrador lo revisará pronto.');
    }
}
