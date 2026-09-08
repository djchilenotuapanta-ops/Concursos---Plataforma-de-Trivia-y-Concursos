<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $q    = trim((string) $request->get('q', ''));
        $role = $request->get('role');

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when(in_array($role, ['admin','company','user','moderator'], true), function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q', 'role'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6'],
            'role' => ['required','in:admin,company,user,moderator'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        return back()->with('success', 'Usuario creado.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required','exists:users,id'],
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255'],
            'role' => ['required','in:admin,company,user,moderator'],

            'password' => ['nullable','string','min:6','confirmed'],
        ]);

        $user = User::findOrFail($data['user_id']);

        if ($user->id === auth()->id() && $data['role'] !== 'admin') {
            return back()->with('error', 'No puedes cambiar tu propio rol.');
        }

        $emailExists = User::where('email', $data['email'])
            ->where('id', '!=', $user->id)
            ->exists();

        if ($emailExists) {
            return back()->with('error', 'Ese email ya está usado por otro usuario.');
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', 'Usuario actualizado.');
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required','exists:users,id'],
        ]);

        if ((int)$data['user_id'] === (int)auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $user = User::findOrFail($data['user_id']);

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'No puedes eliminar el último administrador.');
        }

        $user->active = 0;
        $user->deactivated_at = now();
        $user->deactivation_reason = 'Inactivado por administrador';
        $user->deactivated_by = auth()->id();
        $user->save();

        return back()->with('success', 'Usuario inactivado (no se eliminó de la base).');
    }
}
