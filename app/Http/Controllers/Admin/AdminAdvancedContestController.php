<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ContestBaseController;

class AdminAdvancedContestController extends ContestBaseController
{
	public function show($id)
	{
		$contest = \App\Models\Contest::with(['company', 'prizes'])->findOrFail($id);
		$participants = \App\Models\Participation::where('contest_id', $contest->id)
			->with('user')
			->orderByDesc('last_correct')
			->orderBy('last_finished_at')
			->get()
			->map(function($p) {
				return (object) [
					'user' => $p->user,
					'score' => (int)($p->last_correct ?? 0),
					'seconds' => $p->last_duration_seconds ?? null,
					'is_merit' => ($p->winner_type ?? null) === 'merit',
					'is_lottery' => ($p->winner_type ?? null) === 'lottery',
				];
			});

		// El ganador por mérito es el que tiene winner_type = 'merit' (actualizado por el backend)
		$winner = $participants->first(fn($p) => $p->is_merit);

		return view('admin.contests_adv.show', compact('contest', 'participants', 'winner'));
	}
}
