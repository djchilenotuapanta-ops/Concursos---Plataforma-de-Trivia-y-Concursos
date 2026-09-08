<?php

namespace App\Services;

use App\Models\Contest;
use Illuminate\Database\Eloquent\Builder;

class ContestQueryService
{
    public static function activeContests(): Builder
    {
        return Contest::where('status', 'active')
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }

    public static function upcomingContests(): Builder
    {
        return Contest::where('status', 'draft')
            ->where('start_at', '>', now());
    }

    public static function endedContests(): Builder
    {
        return Contest::where('status', 'ended');
    }

    public static function contestsForCompany(int $companyId): Builder
    {
        return Contest::where('company_id', $companyId)
            ->with(['rules', 'prizes'])
            ->withCount('questions');
    }

    public static function publicContests(): Builder
    {
        return Contest::where('status', 'active')
            ->whereNotNull('winner_published_at')
            ->with(['company:id,name,company_name,razon_social', 'prizes'])
            ->latest('id');
    }
}
