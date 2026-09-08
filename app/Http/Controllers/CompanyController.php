<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\DashboardService;
use App\Services\NotificationService;
use App\Http\Requests\CompanyProfileUpdateRequest;

class CompanyController extends Controller
{
    public function dashboard()
    {
        $stats = DashboardService::getCompanyStats(Auth::user());

        return view('company.dashboard', $stats);
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('company.profile', compact('user'));
    }

    public function updateProfile(CompanyProfileUpdateRequest $request)
    {
        $user = Auth::user();

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('companies/logos', 'public');
        }

        $user->update($data);

        return back()->with('success', '✅ Información guardada.');
    }

    public function subscription()
    {
        $company = Auth::user();
        return view('company.subscription', compact('company'));
    }

    public function paySubscription(Request $request)
    {
        $company = Auth::user();

        $request->validate([
            'voucher_image' => ['required','image','mimes:jpg,jpeg,png,webp','max:4096'],
        ], [
            'voucher_image.required' => 'Debes subir el comprobante (voucher).',
            'voucher_image.image' => 'El archivo debe ser una imagen.',
            'voucher_image.mimes' => 'Formatos permitidos: jpg, jpeg, png, webp.',
            'voucher_image.max' => 'El voucher no debe pesar más de 4MB.',
        ]);

        $path = $request->file('voucher_image')->store('companies/vouchers', 'public');

        $company->voucher_image = $path;

        $company->voucher_approved = 0;

        $company->rejection_reason = null;

        $company->subscription_start = null;
        $company->subscription_end = null;

        $company->save();

        $admins = User::adminsQuery()->get();

        foreach ($admins as $admin) {
            NotificationService::send(
                $admin,
                'Voucher pendiente de aprobación',
                'La empresa "' . ($company->company_name ?? $company->name) . '" subió un comprobante de pago. Revisa para aprobar o rechazar.',
                route('admin.companies.show', $company)
            );
        }

        return back()->with('success', '💳 Comprobante enviado. El administrador lo revisará para activar tu mes.');
    }
}
