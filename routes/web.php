<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminCompanyController;

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyContestController;
use App\Http\Controllers\TriviaQuestionController;
use App\Http\Controllers\TriviaQuestionImportController;
use App\Http\Controllers\TriviaRulesController;
use App\Http\Controllers\PrizeController;

use App\Http\Controllers\ContestController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TriviaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminContactMessageController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\Admin\AdminAdvancedContestController as AdminAdvancedContestController;
use App\Http\Controllers\PublicContestController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/contests', [PublicContestController::class, 'index'])->name('public.contests.index');
Route::get('/contests/{id}/participate', [PublicContestController::class, 'participate'])
    ->middleware(['auth', 'role:user'])
    ->name('public.contests.participate');

Route::post('/contests/{id}/publish', [ContestController::class, 'publish'])->name('contests.publish');
Route::post('/contests/{id}/set-winners', [ContestController::class, 'setWinners'])->name('contests.setWinners');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])
        ->middleware('throttle:10,1')
        ->name('login.post');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.post');

    Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])
        ->name('password.reset');

    Route::post('/reset-password', [PasswordResetController::class, 'updatePassword'])
        ->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if (!$user) return redirect()->route('login');

    $role = strtolower(trim((string) $user->role));
    $role = match ($role) {
        'administrator', 'administrador', 'admin' => 'admin',
        'empresa', 'company' => 'company',
        'moderador', 'moderator' => 'moderator',
        'participante', 'participantes', 'usuario', 'user' => 'user',
        default => $role,
    };

    return match ($role) {
        'admin'   => redirect()->route('admin.dashboard'),
        'company' => redirect()->route('company.dashboard'),
        'moderator' => redirect()->route('moderator.dashboard'),
        default   => redirect()->route('user.dashboard'),
    };
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});

Route::middleware(['auth'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        Route::middleware(['role:admin|moderator', 'moderator.backup'])
            ->controller(AdminAdvancedContestController::class)
            ->prefix('contests-advanced')
            ->as('contests_adv.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{contest}', 'show')->name('show');
                Route::post('/{contest}/winners/publish-manual', 'publishWinnersManual')->name('contests.winners.publish_manual');
            });

        Route::middleware(['role:admin'])->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/metrics', [AdminController::class, 'metrics'])->name('metrics');

        Route::get('/settings', [AdminSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');

        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::put('/reports/{id}/status', [AdminReportController::class, 'updateStatus'])->name('reports.updateStatus');

        Route::get('/contact-messages', [AdminContactMessageController::class, 'index'])->name('contact_messages.index');
        Route::get('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'show'])->name('contact_messages.show');
        Route::post('/contact-messages/{contactMessage}/read', [AdminContactMessageController::class, 'markRead'])->name('contact_messages.read');
        Route::delete('/contact-messages/{contactMessage}', [AdminContactMessageController::class, 'destroy'])->name('contact_messages.destroy');

        Route::controller(AdminUserController::class)
            ->prefix('users')
            ->as('users.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::post('/delete', 'destroy')->name('destroy');
            });

        Route::controller(AdminCompanyController::class)
            ->prefix('companies')
            ->as('companies.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{user}', 'show')->name('show');

                Route::patch('/{user}/approve', 'approveCompany')->name('approve');

                Route::patch('/{user}/active', 'toggleActive')->name('active.toggle');

                Route::patch('/{user}/subscription/approve', 'approveSubscription')->name('subscription.approve');

                Route::patch('/{user}/subscription/reject', 'rejectSubscription')->name('subscription.reject');
            });

        Route::get('/contests', [AdminAdvancedContestController::class, 'index'])->name('contests.index');
        Route::get('/contests/create', [AdminAdvancedContestController::class, 'create'])->name('contests.create');
        Route::post('/contests', [AdminAdvancedContestController::class, 'store'])->name('contests.store');
        Route::post('/contests/{contest}/publish', [AdminAdvancedContestController::class, 'publish'])->name('contests.publish');
        Route::post('/contests/{contest}/winners/publish', [AdminAdvancedContestController::class, 'publishWinners'])->name('contests.winners.publish');
        Route::post('/contests/{contest}/winners/publish-manual', [AdminAdvancedContestController::class, 'publishWinnersManual'])->name('contests.winners.publish_manual');

        Route::prefix('trivia')->as('trivia.')->group(function () {
            Route::get('/{contest}/questions', [TriviaQuestionController::class, 'index'])
                ->defaults('area', 'admin')->name('questions.index');

            Route::get('/{contest}/questions/create', [TriviaQuestionController::class, 'create'])
                ->defaults('area', 'admin')->name('questions.create');

            Route::post('/{contest}/questions', [TriviaQuestionController::class, 'store'])
                ->defaults('area', 'admin')->name('questions.store');

            Route::delete('/{contest}/questions/{question}', [TriviaQuestionController::class, 'destroy'])
                ->defaults('area', 'admin')->name('questions.destroy');

            Route::get('/{contest}/questions/import', [TriviaQuestionImportController::class, 'form'])
                ->defaults('area', 'admin')->name('questions.import.form');

            Route::post('/{contest}/questions/import', [TriviaQuestionImportController::class, 'import'])
                ->defaults('area', 'admin')->name('questions.import');

            Route::get('/{contest}/questions/import/select', [TriviaQuestionImportController::class, 'select'])
                ->defaults('area', 'admin')->name('questions.import.select');

            Route::post('/{contest}/questions/import/commit', [TriviaQuestionImportController::class, 'commit'])
                ->defaults('area', 'admin')->name('questions.import.commit');

            Route::get('/{contest}/questions/template', [TriviaQuestionImportController::class, 'template'])
                ->defaults('area', 'admin')->name('questions.template');


Route::get('/{contest}/rules', [TriviaRulesController::class, 'edit'])
    ->defaults('area', 'admin')->name('rules.edit');

Route::post('/{contest}/rules', [TriviaRulesController::class, 'update'])
    ->defaults('area', 'admin')->name('rules.update');
        });

        });

    });

Route::middleware(['auth', 'role:moderator'])
    ->prefix('moderator')
    ->as('moderator.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return redirect()->route('moderator.contests.index');
        })->name('dashboard');

        Route::get('/contests', [\App\Http\Controllers\Moderator\ModeratorContestController::class, 'index'])->name('contests.index');
        Route::get('/contests/create', [\App\Http\Controllers\Moderator\ModeratorContestController::class, 'create'])->name('contests.create');
        Route::post('/contests', [\App\Http\Controllers\Moderator\ModeratorContestController::class, 'store'])->name('contests.store');
        Route::post('/contests/{contest}/publish', [\App\Http\Controllers\Moderator\ModeratorContestController::class, 'publish'])->name('contests.publish');
        Route::post('/contests/{contest}/winners/publish', [\App\Http\Controllers\Moderator\ModeratorContestController::class, 'publishWinners'])->name('contests.winners.publish');

        Route::prefix('trivia')->as('trivia.')->group(function () {
            Route::get('/{contest}/questions', [\App\Http\Controllers\TriviaQuestionController::class, 'index'])
                ->defaults('area', 'moderator')->name('questions.index');

            Route::get('/{contest}/questions/create', [\App\Http\Controllers\TriviaQuestionController::class, 'create'])
                ->defaults('area', 'moderator')->name('questions.create');

            Route::post('/{contest}/questions', [\App\Http\Controllers\TriviaQuestionController::class, 'store'])
                ->defaults('area', 'moderator')->name('questions.store');

            Route::delete('/{contest}/questions/{question}', [\App\Http\Controllers\TriviaQuestionController::class, 'destroy'])
                ->defaults('area', 'moderator')->name('questions.destroy');

            Route::get('/{contest}/questions/import', [\App\Http\Controllers\TriviaQuestionImportController::class, 'form'])
                ->defaults('area', 'moderator')->name('questions.import.form');

            Route::post('/{contest}/questions/import', [\App\Http\Controllers\TriviaQuestionImportController::class, 'import'])
                ->defaults('area', 'moderator')->name('questions.import');

            Route::get('/{contest}/questions/import/select', [\App\Http\Controllers\TriviaQuestionImportController::class, 'select'])
                ->defaults('area', 'moderator')->name('questions.import.select');

            Route::post('/{contest}/questions/import/commit', [\App\Http\Controllers\TriviaQuestionImportController::class, 'commit'])
                ->defaults('area', 'moderator')->name('questions.import.commit');

            Route::get('/{contest}/questions/template', [\App\Http\Controllers\TriviaQuestionImportController::class, 'template'])
                ->defaults('area', 'moderator')->name('questions.template');

            Route::get('/{contest}/rules', [\App\Http\Controllers\TriviaRulesController::class, 'edit'])
                ->defaults('area', 'moderator')->name('rules.edit');

            Route::post('/{contest}/rules', [\App\Http\Controllers\TriviaRulesController::class, 'update'])
                ->defaults('area', 'moderator')->name('rules.update');
        });
    });

Route::middleware(['auth', 'role:company', 'company.active'])
    ->prefix('company')
    ->as('company.')
    ->group(function () {

        Route::get('/dashboard', [CompanyController::class, 'dashboard'])->name('dashboard');

        Route::get('/profile', [CompanyController::class, 'editProfile'])->name('profile.edit');
        Route::post('/profile', [CompanyController::class, 'updateProfile'])->name('profile.update');

        Route::get('/subscription', [CompanyController::class, 'subscription'])->name('subscription');
        Route::post('/subscription/pay', [CompanyController::class, 'paySubscription'])
            ->middleware('company.profile')
            ->name('subscription.pay');

        Route::get('/contests', [CompanyContestController::class, 'index'])->name('contests.index');

        Route::get('/contests/create', [CompanyContestController::class, 'create'])
            ->middleware(['company.profile', 'company.approved'])
            ->name('contests.create');

        Route::post('/contests', [CompanyContestController::class, 'store'])
            ->middleware(['company.profile', 'company.approved'])
            ->name('contests.store');

        Route::post('/contests/{contest}/publish', [CompanyContestController::class, 'publish'])
            ->middleware(['company.profile', 'company.approved'])
            ->name('contests.publish');

        Route::post('/contests/{contest}/winners/publish', [CompanyContestController::class, 'publishWinners'])
            ->middleware(['company.profile', 'company.approved'])
            ->name('contests.winners.publish');

        Route::get('/prizes', [PrizeController::class, 'companyIndex'])->name('prizes.index');
        Route::post('/prizes/{prize}/deliver', [PrizeController::class, 'deliver'])->name('prizes.deliver');

        Route::prefix('trivia')->as('trivia.')->group(function () {
            Route::get('/{contest}/questions', [TriviaQuestionController::class, 'index'])
                ->defaults('area', 'company')->name('questions.index');

            Route::get('/{contest}/questions/create', [TriviaQuestionController::class, 'create'])
                ->defaults('area', 'company')->name('questions.create');

            Route::post('/{contest}/questions', [TriviaQuestionController::class, 'store'])
                ->defaults('area', 'company')->name('questions.store');

            Route::delete('/{contest}/questions/{question}', [TriviaQuestionController::class, 'destroy'])
                ->defaults('area', 'company')->name('questions.destroy');

            Route::get('/{contest}/questions/import', [TriviaQuestionImportController::class, 'form'])
                ->defaults('area', 'company')->name('questions.import.form');

            Route::post('/{contest}/questions/import', [TriviaQuestionImportController::class, 'import'])
                ->defaults('area', 'company')->name('questions.import');

            Route::get('/{contest}/questions/import/select', [TriviaQuestionImportController::class, 'select'])
                ->defaults('area', 'company')->name('questions.import.select');

            Route::post('/{contest}/questions/import/commit', [TriviaQuestionImportController::class, 'commit'])
                ->defaults('area', 'company')->name('questions.import.commit');

            Route::get('/{contest}/questions/template', [TriviaQuestionImportController::class, 'template'])
                ->defaults('area', 'company')->name('questions.template');


Route::get('/{contest}/rules', [TriviaRulesController::class, 'edit'])
    ->defaults('area', 'company')->name('rules.edit');

Route::post('/{contest}/rules', [TriviaRulesController::class, 'update'])
    ->defaults('area', 'company')->name('rules.update');
        });
    });

Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->as('user.')
    ->group(function () {

        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

        Route::get('/history', [UserController::class, 'history'])->name('history');

        Route::get('/contests', [ContestController::class, 'list'])->name('contests.list');

        Route::post('/contests/{id}/participate', [ParticipationController::class, 'store'])
            ->name('contests.participate');

        Route::controller(TriviaController::class)
            ->prefix('trivia')
            ->as('trivia.')
            ->group(function () {
                Route::post('/{contest}/start', 'start')->name('start');
                Route::get('/{contest}/play', 'play')->name('play');
                Route::post('/{contest}/timeout', 'timeout')->name('timeout');
                Route::post('/{contest}/answer', 'answer')->name('answer');
                Route::get('/{contest}/result', 'result')->name('result');
            });

        Route::get('/results', function () {
            $user = auth()->user();

            $prizes = \App\Models\Prize::with('contest')
                ->where('winner_user_id', $user->id)
                ->get();

            return view('user.results', compact('prizes'));
        })->name('results');

        Route::post('/prizes/{prize}/claim', [PrizeController::class, 'claim'])->name('prizes.claim');
    });

Route::middleware('auth')
    ->get('/notifications', [NotificationController::class, 'index'])
    ->name('notifications.index');

Route::middleware('auth')->group(function () {
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});
