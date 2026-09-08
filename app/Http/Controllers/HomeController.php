<?php

namespace App\Http\Controllers;

use App\Models\Prize;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $deliveredPrizes = Prize::with(['contest', 'winner'])
            ->whereNotNull('delivered_at')
            ->orderByDesc('delivered_at')
            ->take(9)
            ->get()
            ->map(function ($p) {
                $p->image_url = $p->image_path && Storage::disk('public')->exists($p->image_path)
                    ? Storage::disk('public')->url($p->image_path)
                    : asset('frontend/assets/img/portfolio/1.jpg');

                return $p;
            });

        return view('app.front.index', compact('deliveredPrizes'));
    }
}
