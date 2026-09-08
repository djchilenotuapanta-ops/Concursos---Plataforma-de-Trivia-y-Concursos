<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\Participation;
use App\Models\Prize;
use App\Services\DashboardService;

class UserController extends Controller
{
    public function dashboard()
    {
        $stats = DashboardService::getUserStats(auth()->user());

        $latestContests = Contest::orderByDesc('id')->take(5)->get();

        return view('user.dashboard', array_merge($stats, compact('latestContests')));
    }

    public function history()
    {
        $user = auth()->user();

        $participations = Participation::with(['contest'])
            ->where('user_id', $user->id)
            ->latest('joined_at')
            ->paginate(20);

        $wonByContest = Prize::where('winner_user_id', $user->id)
            ->get()
            ->groupBy('contest_id');

        $contestIds = $participations->getCollection()->pluck('contest_id')->filter()->unique()->values();
        $winnerByContest = Prize::with(['winner'])
            ->whereIn('contest_id', $contestIds)
            ->whereNotNull('winner_user_id')
            ->orderBy('position')
            ->get()
            ->groupBy('contest_id');

        return view('user.history', compact('participations', 'wonByContest', 'winnerByContest'));
    }
}
