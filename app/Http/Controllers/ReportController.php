<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Notifications\DbNotification;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'contest_id' => ['nullable','exists:contests,id'],
            'reason' => ['nullable','string','max:120'],
            'message' => ['required','string','min:10','max:2000'],
        ], [
            'message.required' => '⚠️ Escribe el detalle de tu queja (mínimo 10 caracteres).',
            'message.min' => '⚠️ Escribe un poco más de detalle (mínimo 10 caracteres).',
        ]);

        $report = Report::create([
            'user_id' => auth()->id(),
            'contest_id' => $data['contest_id'] ?? null,
            'reason' => $data['reason'] ?? 'contenido inapropiado',
            'message' => $data['message'],
            'status' => 'open',
        ]);

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new DbNotification([
                'title' => '⚠️ Reporte de contenido',
                'message' => 'Un usuario reportó posible contenido inapropiado. Revisa el reporte #'.$report->id.'.',
                'url' => route('admin.reports.index'),
            ]));
        }

        return back()->with('success', 'Gracias. Tu reporte fue enviado al administrador.');
    }
}
