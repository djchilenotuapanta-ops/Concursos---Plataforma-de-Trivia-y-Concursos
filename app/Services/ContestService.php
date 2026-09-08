<?php

namespace App\Services;

use App\Models\Contest;

class ContestService
{
    public static function getLatestActive(int $limit = 5)
    {
        return Contest::where('status', 'active')
            ->orderByDesc('id')
            ->take($limit)
            ->get();
    }

    public static function getContestsForRole(string $role, ?int $companyId = null)
    {
        $query = Contest::with(['company', 'prizes']);

        if ($role === 'company' && $companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->orderByDesc('id')->get();
    }
}
