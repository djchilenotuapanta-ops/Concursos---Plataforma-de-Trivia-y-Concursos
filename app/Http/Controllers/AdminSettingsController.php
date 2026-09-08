<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function edit()
    {
        $settings = [
            'ranking_top_n' => SystemSetting::getInt('ranking_top_n', config('ganafacil.ranking_top_n', 5)),
            'trivia_max_total_seconds' => SystemSetting::getInt('trivia_max_total_seconds', config('ganafacil.trivia_max_total_seconds', 3600)),
            'max_years_in_future' => SystemSetting::getInt('max_years_in_future', config('ganafacil.max_years_in_future', 5)),
            'moderator_backup_mode' => (int) SystemSetting::getValue('moderator_backup_mode', '0'),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'ranking_top_n' => ['required','integer','min:1','max:50'],
            'trivia_max_total_seconds' => ['required','integer','min:30','max:86400'],
            'max_years_in_future' => ['required','integer','min:1','max:20'],
            'moderator_backup_mode' => ['nullable','in:0,1'],
        ], [
            'ranking_top_n.required' => '⚠️ Debes indicar cuántos elementos se mostrarán en el ranking.',
            'trivia_max_total_seconds.required' => '⚠️ Debes indicar el límite máximo de tiempo de trivia.',
            'max_years_in_future.required' => '⚠️ Debes indicar el límite de años permitidos.',
        ]);

        SystemSetting::put('ranking_top_n', (int) $data['ranking_top_n']);
        SystemSetting::put('trivia_max_total_seconds', (int) $data['trivia_max_total_seconds']);
        SystemSetting::put('max_years_in_future', (int) $data['max_years_in_future']);
        SystemSetting::put('moderator_backup_mode', (int) ($data['moderator_backup_mode'] ?? 0));

        return back()->with('success', 'La información fue actualizada.');
    }
}
