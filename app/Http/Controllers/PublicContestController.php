<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use Illuminate\Http\Request;

class PublicContestController extends Controller
{
    public function index()
    {
        $now = now();

        $contests = Contest::with(['company:id,name', 'rules'])
            ->withCount('questions')
            ->where('type','trivia')
            ->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            })
            ->orderByDesc('id')
            ->get();

        return view('app.front.contests', compact('contests'));
    }

    public function participate(Request $request, $id)
    {
        return app(ParticipationController::class)->store($request, $id);
    }
}
