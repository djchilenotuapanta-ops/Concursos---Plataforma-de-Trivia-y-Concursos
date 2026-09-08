<?php

namespace App\Services;

use App\Models\User;
use App\Models\Contest;
use App\Models\Participation;
use App\Models\Prize;
use App\Models\ContestQuestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\CompanyService;

class DashboardService
{
    public static function getAdminStats(): array
    {
        $companyStats = CompanyService::countByStatus();

        return [
            'contests'  => Contest::count(),
            'users'     => User::where('role', 'user')->count(),
            'companies' => $companyStats['total'],
            'pending_companies'  => $companyStats['pending'],
            'approved_companies' => $companyStats['approved'],
            'pending_vouchers' => CompanyService::getPendingVouchers()->count(),
            'participants' => Schema::hasTable('participations')
                ? DB::table('participations')->count()
                : 0,
        ];
    }

    public static function getUserStats(User $user): array
    {
        $activeContests = Contest::where('status', 'active')->count();
        $myParticipations = Participation::where('user_id', $user->id)->count();
        $myPrizes = Prize::where('winner_user_id', $user->id)->count();

        return [
            'activeContests' => $activeContests,
            'myParticipations' => $myParticipations,
            'myPrizes' => $myPrizes,
            'barChart' => [
                'labels' => ['Activos', 'Participaciones', 'Premios'],
                'values' => [(int) $activeContests, (int) $myParticipations, (int) $myPrizes],
            ],
        ];
    }

    public static function getCompanyStats(User $company): array
    {
        $companyContestsQuery = Contest::where('company_id', $company->id);
        $companyContestIds = (clone $companyContestsQuery)->pluck('id');

        $totalContests = (clone $companyContestsQuery)->count();
        $activeContests = (clone $companyContestsQuery)->where('status', 'active')->count();
        $endedContests = (clone $companyContestsQuery)->where('status', 'ended')->count();
        $totalParticipations = Participation::whereIn('contest_id', $companyContestIds)->count();
        $totalPrizes = Prize::whereIn('contest_id', $companyContestIds)->count();
        $deliveredPrizes = Prize::whereIn('contest_id', $companyContestIds)->whereNotNull('delivered_at')->count();
        $totalQuestions = ContestQuestion::whereIn('contest_id', $companyContestIds)->count();

        return [
            'totalContests' => $totalContests,
            'activeContests' => $activeContests,
            'endedContests' => $endedContests,
            'totalParticipations' => $totalParticipations,
            'totalPrizes' => $totalPrizes,
            'deliveredPrizes' => $deliveredPrizes,
            'totalQuestions' => $totalQuestions,
            'barChart' => [
                'labels' => ['Concursos', 'Activos', 'Participaciones', 'Premios', 'Entregados', 'Preguntas'],
                'values' => [
                    (int) $totalContests,
                    (int) $activeContests,
                    (int) $totalParticipations,
                    (int) $totalPrizes,
                    (int) $deliveredPrizes,
                    (int) $totalQuestions,
                ],
            ],
        ];
    }
}
