<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UserProfileUpdateRequest;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(UserProfileUpdateRequest $request)
    {
        $user = auth()->user();

        $data = $request->validated();

        $cleanId = preg_replace('/\D+/', '', (string) $data['cedula']);

        $user->forceFill([
            'birthdate' => $data['birthdate'],
            'cedula' => $cleanId,
            'phone' => $data['phone'] ?? null,
        ])->save();

        return back()->with('success', 'Perfil actualizado. Tu edad se calcula automáticamente según tu fecha de nacimiento.');
    }

    public function updateAvatar(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'in:1'],
        ]);

        if (($data['remove_avatar'] ?? null) == '1') {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = null;
            $user->save();

            return back()->with('success', 'Foto eliminada.');
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_path = $path;
            $user->save();

            return back()->with('success', 'Foto de perfil actualizada.');
        }

        return back()->with('info', 'No se subió ninguna foto.');
    }
}
