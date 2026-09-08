<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Services\DashboardService;
use App\Services\MetricsService;
use App\Services\CompanyService;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = DashboardService::getAdminStats();

        return view('admin.dashboard', compact('stats'));
    }

    public function metrics(Request $request)
    {
        $period = $request->get('period', 'month');
        $period = in_array($period, ['day','month']) ? $period : 'month';

        $date = $request->get('date');
        $base = $date ? Carbon::parse($date) : now();

        if ($period === 'day') {
            $start = $base->copy()->startOfDay();
            $end   = $base->copy()->endOfDay();
            $prevStart = $start->copy()->subDay();
            $prevEnd   = $end->copy()->subDay();
            $label = $start->format('d/m/Y');
            $compareLabel = $prevStart->format('d/m/Y');
        } else {
            $start = $base->copy()->startOfMonth();
            $end   = $base->copy()->endOfMonth();
            $prevStart = $start->copy()->subMonth()->startOfMonth();
            $prevEnd   = $start->copy()->subMonth()->endOfMonth();
            $label = $start->translatedFormat('F Y');
            $compareLabel = $prevStart->translatedFormat('F Y');
        }

                $topN = SystemSetting::getInt('ranking_top_n', config('ganafacil.ranking_top_n', 5));

        $participantsNow = MetricsService::getParticipationsCount($start, $end);
        $participantsPrev = MetricsService::getParticipationsCount($prevStart, $prevEnd);

        $incomeNow = MetricsService::getIncome($start, $end);
        $incomePrev = MetricsService::getIncome($prevStart, $prevEnd);

        $subscriptionFee = (float) config('metrics.subscription_fee', 10.00);
        $subsNow = CompanyService::getSubscriptionStats($start, $end);
        $subsPrev = CompanyService::getSubscriptionStats($prevStart, $prevEnd);

        $incomeNow += $subsNow['revenue'];
        $incomePrev += $subsPrev['revenue'];

        $marginNow = $incomeNow;
        $marginPrev = $incomePrev;

        $ratingNow = MetricsService::getAverageRating($start, $end);
        $ratingPrev = MetricsService::getAverageRating($prevStart, $prevEnd);

        $loginsNow = Schema::hasColumn('users','last_login_at')
            ? User::whereBetween('last_login_at', [$start, $end])->count()
            : 0;

        $topByParticipants = MetricsService::getTopContestsByParticipants($start, $end, $topN);
        $topByIncome = MetricsService::getTopContestsByIncome($start, $end, $topN);

        $alerts = [];
        if ($period === 'day') {
            $avg7 = MetricsService::getSevenDayAverage(now());
            if ($avg7 > 0 && $participantsNow < $avg7) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Participacion baja',
                    'message' => 'La participacion del dia esta por debajo del promedio de los ultimos 7 dias. Revisa premios o promocion.'
                ];
            }
        }
        $stale = Contest::where('status','active')
            ->where('start_at','<=',now())
            ->where('end_at','>=',now())
            ->whereDoesntHave('participants', function ($q) {
                $q->where('participations.created_at','>=', now()->subHours(24));
            })
            ->limit(3)
            ->get();
        foreach ($stale as $c) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Concurso sin inscripciones',
                'message' => 'El concurso "'.$c->title.'" no ha recibido inscripciones en las ultimas 24 horas.'
            ];
        }

        $kpis = [
            'participants' => [
                'now' => $participantsNow,
                'prev' => $participantsPrev,
            ],
            'income' => [
                'now' => $incomeNow,
                'prev' => $incomePrev,
            ],
            'margin' => [
                'now' => $marginNow,
                'prev' => $marginPrev,
            ],
            'rating' => [
                'now' => $ratingNow,
                'prev' => $ratingPrev,
            ],
        ];

        return view('admin.metrics', [
            'period' => $period,
            'label' => $label,
            'compareLabel' => $compareLabel,
            'date' => $start->toDateString(),
            'kpis' => $kpis,
            'loginsNow' => $loginsNow,
            'topByParticipants' => $topByParticipants,
            'topByIncome' => $topByIncome,
            'topN' => $topN,
            'alerts' => $alerts,
            'subscriptionFee' => $subscriptionFee,
            'subsNow' => $subsNow,
        ]);
    }
}
