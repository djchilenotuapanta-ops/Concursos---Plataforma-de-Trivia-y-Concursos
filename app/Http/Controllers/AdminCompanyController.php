<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\NotificationService;
use App\Services\UserService;
use App\Services\CompanyService;
use Illuminate\Http\Request;

class AdminCompanyController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $totalCompanies = User::where('role', 'company')->count();

        $companies = User::where('role', 'company')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . $q . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('company_name', 'like', $like)
                        ->orWhere('razon_social', 'like', $like)
                        ->orWhere('ruc', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('name', 'like', $like)
                        ->orWhere('representative_name', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                });
            })
            ->orderByDesc('created_at')
            ->get();

        return view('admin.companies.index', compact('companies', 'q', 'totalCompanies'));
    }

    public function approveCompany(User $user)
    {
        if ($user->role !== 'company') {
            return back()->with('error', 'Este usuario no es una empresa.');
        }

        $user->approved = 1;
        $user->active = 1;
        $user->save();

        NotificationService::companyApproved($user);

        return back()->with('success', '✅ Empresa aprobada correctamente.');
    }

    public function show(User $user)
    {
        if ($user->role !== 'company') {
            return back()->with('error', 'Este usuario no es una empresa.');
        }

        return view('admin.companies.show', compact('user'));
    }

    public function approve(User $user)
    {
        return $this->approveVoucher($user);
    }

    public function reject(Request $request, User $user)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ], [
            'rejection_reason.required' => 'Debes escribir el motivo del rechazo.',
        ]);

        if ($user->role !== 'company') {
            return back()->with('error', 'Este usuario no es una empresa.');
        }

        return $this->rejectSubscription($request, $user);
    }

    public function approveVoucher(User $user)
    {
        if ($user->role !== 'company') {
            return back()->with('error', 'Este usuario no es una empresa.');
        }

        if (!$user->voucher_image) {
            return back()->with('error', 'La empresa no ha subido ningún voucher.');
        }

        $user->voucher_approved = 1;
        $user->rejection_reason = null;

        $user->active = 1;
        $user->deactivated_at = null;
        $user->deactivation_reason = null;
        $user->deactivated_by = null;

        UserService::activateSubscriptionForOneMonth($user);

        $user->save();

        NotificationService::send($user, 'Suscripción aprobada 💳', 'Tu voucher fue aprobado. Ya puedes crear y publicar concursos. Tu suscripción está activa hasta ' . optional($user->subscription_end)->format('Y-m-d H:i'), route('company.dashboard'));

        $actor = auth()->user();
        User::adminsQuery()->get()->each(function ($a) use ($user, $actor) {
            NotificationService::send($a, '💳 Voucher aprobado', 'Se aprobó el voucher de la empresa "' . ($user->company_name ?? $user->name) . '" por ' . (($actor->email ?? null) ?: 'un administrador') . '.', route('admin.companies.show', $user));
        });

        return back()->with('success', '💳 Voucher aprobado. Suscripción activa por 1 mes.');
    }

    public function approveSubscription(User $user)
    {
        return $this->approveVoucher($user);
    }

    public function rejectSubscription(Request $request, User $user)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|min:5',
        ]);

        if ($user->role !== 'company') {
            return back()->with('error', 'Este usuario no es una empresa.');
        }

        if (!$user->voucher_image) {
            return back()->with('error', 'La empresa no ha subido ningún voucher.');
        }

        $user->voucher_approved = 2;
        $user->rejection_reason = $request->rejection_reason ?: 'Voucher rechazado por el administrador.';

        $user->subscription_start = null;
        $user->subscription_end = null;
        $user->save();

        NotificationService::send($user, 'Suscripción rechazada ❌', 'Motivo: ' . $user->rejection_reason, route('company.subscription'));

        $actor = auth()->user();
        User::adminsQuery()->get()->each(function ($a) use ($user, $actor) {
            NotificationService::send($a, '❌ Voucher rechazado', 'Se rechazó el voucher de la empresa "' . ($user->company_name ?? $user->name) . '" por ' . (($actor->email ?? null) ?: 'un administrador') . '. Motivo: ' . ($user->rejection_reason ?: 'N/D') . '.', route('admin.companies.show', $user));
        });

        return back()->with('success', '❌ Voucher rechazado. Suscripción no activada.');
    }

    public function rejectVoucher(Request $request, User $user)
    {
        if ($user->role !== 'company') {
            return back()->with('error', 'Este usuario no es una empresa.');
        }

        if (!$user->voucher_image) {
            return back()->with('error', 'La empresa no ha subido ningún voucher.');
        }

        $user->voucher_approved = 2;

        $user->subscription_start = null;
        $user->subscription_end = null;

        $user->save();

        return back()->with('success', '❌ Voucher rechazado. Suscripción no activada.');
    }

    public function toggleActive(Request $request, User $user)
    {
        if ($user->role !== 'company') {
            return back()->with('error', 'Este usuario no es una empresa.');
        }

        $makeActive = $request->boolean('active'); // si viene active=1 => activar
        $reason = trim((string) $request->input('reason', ''));

        if ($makeActive) {
            $user->active = 1;
            $user->deactivated_at = null;
            $user->deactivation_reason = null;
            $user->deactivated_by = null;
            $user->save();

            NotificationService::companyReactivated($user);

            return back()->with('success', 'Empresa activada correctamente.');
        }

        if ($reason === '') {
            return back()->with('error', 'Para inactivar, escribe un motivo (ej: "Queja de usuario / revisión pendiente").');
        }

        $user->active = 0;
        $user->deactivated_at = now();
        $user->deactivation_reason = $reason;
        $user->deactivated_by = auth()->id();
        $user->save();

        NotificationService::send($user, 'Empresa inactivada', 'Tu empresa fue inactivada por el administrador. Motivo: '.$reason, null);

        return back()->with('success', 'Empresa inactivada correctamente.');
    }

}
