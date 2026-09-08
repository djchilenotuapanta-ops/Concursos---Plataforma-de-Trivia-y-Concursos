<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\DbNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Credenciales incorrectas.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user) {
            $user->forceFill(['last_login_at' => now()])->save();

            if ((int)($user->active ?? 1) !== 1) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Tu cuenta está inactiva.');
            }
        }

        return redirect()->route('dashboard');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'string'],
            'role'     => ['required', 'in:user,company'],
        ]);

        $payload = [
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'password' => Hash::make($data['password']),
        ];

        if ($data['role'] === 'company') {
            $payload['approved'] = 1;
            $payload['active'] = 1;
            $payload['voucher_approved'] = 0;
        }

        $user = User::create($payload);

        if (($user->role ?? null) === 'company') {
            User::adminsQuery()->get()->each(function ($a) use ($user) {
                $a->notify(new DbNotification([
                    'title' => '🏢 Nueva empresa registrada',
                    'message' => 'Se registró la empresa: "' . ($user->company_name ?? $user->name) . '" (' . $user->email . ').',
                    'url' => route('admin.companies.show', $user),
                ]));
            });
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
