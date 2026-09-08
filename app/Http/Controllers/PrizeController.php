<?php

namespace App\Http\Controllers;

use App\Models\Prize;
use App\Models\User;
use App\Notifications\DbNotification;
use Illuminate\Http\Request;

class PrizeController extends Controller
{
    public function companyIndex()
    {
        $companyId = auth()->id();

        $prizes = Prize::with(['contest', 'winner'])
            ->whereHas('contest', fn ($q) => $q->where('company_id', $companyId))
            ->orderByDesc('id')
            ->get();

        return view('company.prizes.index', compact('prizes'));
    }

    public function deliver(Request $request, Prize $prize)
    {
        $companyId = auth()->id();
        abort_unless((int) $prize->contest->company_id === (int) $companyId, 403);

        if (!$prize->claimed_at) {
            return back()->with('error', 'El ganador aún no ha reclamado este premio.');
        }

        if (!$prize->delivered_at) {
            $prize->delivered_at = now();
            $prize->save();

            if ($prize->winner) {
                $prize->winner->notify(new DbNotification([
                    'title' => '✅ Premio entregado',
                    'message' => 'La empresa marcó como entregado tu premio "' . ($prize->name ?? 'Premio') . '" del concurso "' . ($prize->contest->title ?? 'Concurso') . '".',
                    'url' => route('user.results'),
                ]));
            }
        }

        return back()->with('success', 'Listo ✅ Premio marcado como entregado.');
    }

    public function claim(Request $request, Prize $prize)
    {
        $user = auth()->user();

        abort_unless((int) $prize->winner_user_id === (int) $user->id, 403);

        if (!$prize->claimed_at) {
            $prize->claimed_at = now();
            $prize->save();

            $company = User::find($prize->contest->company_id);
            if ($company) {
                $company->notify(new DbNotification([
                    'title' => '🏆 Premio reclamado',
                    'message' => 'El ganador "' . $user->name . '" reclamó el premio "' . ($prize->name ?? 'Premio') . '" del concurso "' . ($prize->contest->title ?? 'Concurso') . '".',
                    'url' => route('company.prizes.index'),
                ]));
            }
        }

        return back()->with('success', 'Listo ✅ Se registró el reclamo del premio. La empresa será notificada.');
    }
}
