<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CompanyService
{
    public static function getCompanies(): Builder
    {
        return User::where('role', 'company')
            ->orderBy('company_name')
            ->orderBy('name');
    }

    public static function getActiveCompanies(): Builder
    {
        return static::getCompanies()
            ->where('active', 1)
            ->where('voucher_approved', 1);
    }

    public static function getPendingVouchers(): Builder
    {
        return User::where('role', 'company')
            ->where('voucher_approved', 0)
            ->whereNotNull('voucher_image');
    }

    public static function countByStatus(): array
    {
        $total = User::where('role', 'company')->count();
        $pending = User::where('role', 'company')->where('voucher_approved', 0)->count();
        $approved = User::where('role', 'company')->where('voucher_approved', 1)->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
        ];
    }

    public static function getSubscriptionStats(string $start, string $end): array
    {
        $subscriptionFee = (float) config('metrics.subscription_fee', 10.00);

        $count = User::where('role','company')
            ->where('voucher_approved', 1)
            ->whereNotNull('subscription_start')
            ->whereBetween('subscription_start', [$start, $end])
            ->count();

        return [
            'count' => $count,
            'revenue' => $count * $subscriptionFee,
        ];
    }
}
