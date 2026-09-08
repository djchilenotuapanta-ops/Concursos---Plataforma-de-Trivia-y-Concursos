<?php

namespace App\Services;

use App\Models\Participation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class MetricsService
{
    public static function getParticipationsCount(Carbon $start, Carbon $end): int
    {
        if (!Schema::hasTable('participations')) {
            return 0;
        }

        return DB::table('participations')
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    public static function getIncome(Carbon $start, Carbon $end): float
    {
        if (!Schema::hasTable('participations') || !Schema::hasTable('contests')) {
            return 0.0;
        }

        return (float) DB::table('participations')
            ->join('contests', 'contests.id', '=', 'participations.contest_id')
            ->whereBetween('participations.created_at', [$start, $end])
            ->selectRaw('COALESCE(SUM(participations.tickets * contests.ticket_price),0) as total')
            ->value('total');
    }

    public static function getAverageRating(Carbon $start, Carbon $end): ?float
    {
        if (!Schema::hasColumn('participations', 'rating')) {
            return null;
        }

        return DB::table('participations')
            ->whereNotNull('rating')
            ->whereBetween('created_at', [$start, $end])
            ->avg('rating');
    }

    public static function getTopContestsByParticipants(Carbon $start, Carbon $end, int $limit = 5): array
    {
        if (!Schema::hasTable('participations')) {
            return [];
        }

        return DB::table('participations')
            ->join('contests','contests.id','=','participations.contest_id')
            ->whereBetween('participations.created_at', [$start, $end])
            ->groupBy('contests.id','contests.title','contests.type')
            ->selectRaw('contests.id, contests.title, contests.type, COUNT(*) as participations_count')
            ->orderByDesc('participations_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public static function getTopContestsByIncome(Carbon $start, Carbon $end, int $limit = 5): array
    {
        if (!Schema::hasTable('participations')) {
            return [];
        }

        return DB::table('participations')
            ->join('contests','contests.id','=','participations.contest_id')
            ->whereBetween('participations.created_at', [$start, $end])
            ->groupBy('contests.id','contests.title','contests.type','contests.ticket_price')
            ->selectRaw('contests.id, contests.title, contests.type, COALESCE(SUM(participations.tickets * contests.ticket_price),0) as total_income')
            ->orderByDesc('total_income')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public static function getSevenDayAverage(Carbon $endDate): float
    {
        if (!Schema::hasTable('participations')) {
            return 0.0;
        }

        return (float) DB::table('participations')
            ->whereBetween('created_at', [$endDate->copy()->subDays(7)->startOfDay(), $endDate->copy()->endOfDay()])
            ->selectRaw('COUNT(*)/7 as avg7')
            ->value('avg7');
    }
}
