<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Contest;
use App\Models\Participation;
use App\Models\TriviaProgress;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\TriviaAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;

class TriviaController extends Controller
{

    private function notifyNeedBirthdate(User $user): void
    {
        NotificationService::profileComplete($user);
    }

    private function redirectNeedBirthdate(string $message): RedirectResponse
    {
        $user = auth()->user();
        if ($user) {
            $this->notifyNeedBirthdate($user);
        }

        return redirect()->route('profile.edit')->with('error', $message);
    }


    private function allowedQuestionAudiences(Contest $contest, User $user): array
    {
        $hasSegmentedQuestions = $contest->questions()
            ->where('audience', '!=', 'all')
            ->exists();

        $aud = ['all'];

        if ($user->birthdate) {
            if (($user->age ?? 0) >= 18) {
                $aud[] = 'adult';
            } else {
                $aud[] = 'kid';
            }
        }

        return $aud;
    }
    private function guardAllowedForUser(Contest $contest): ?RedirectResponse
    {
        $user = auth()->user();

        try {
            TriviaAccessService::validateContestAccess($contest);
            TriviaAccessService::validateUserAge($contest, $user);
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }

        $rules = $contest->rules;
        $ageGroup = $rules?->age_group ?? 'all';
        $onlyAdults = (bool)($rules?->only_adults ?? false) || $ageGroup === 'adults';

        if ($onlyAdults || $ageGroup === 'kids') {
            if (!$user?->birthdate) {
                return $this->redirectNeedBirthdate('Para participar, primero registra tu fecha de nacimiento en tu perfil.');
            }
        }

        return null;
    }

    public function start(Contest $contest)
    {
        $user = auth()->user();

        abort_unless($contest->type === 'trivia', 404);

        $rules = $contest->rules;
        abort_unless($rules, 403, 'Faltan reglas del concurso');

        if ($resp = $this->guardAllowedForUser($contest)) {
            return $resp;
        }

        if (!is_null($rules->max_participants)) {
            $alreadyIn = Participation::where('contest_id', $contest->id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$alreadyIn) {
                $current = Participation::where('contest_id', $contest->id)->count();
                if ($current >= (int) $rules->max_participants) {
                    abort(403, '⛔ Cupos agotados. Ya no se aceptan más participantes.');
                }
            }
        }

        $participation = Participation::firstOrCreate(
            ['contest_id' => $contest->id, 'user_id' => $user->id],
            [
                'tickets' => 1,
                'status' => 'active',
                'cedula_snapshot' => $user->cedula,
                'joined_at' => now(),
            ]
        );

        if ($participation->wasRecentlyCreated) {
            NotificationService::triviaJoined($user, $contest);
        }

        if (!is_null($rules->attempts) && $participation->attempts_used >= $rules->attempts) {
            abort(403, 'Has agotado tus intentos');
        }

        $participation->attempts_used += 1;
        $participation->attempt_started_at = now();
        $totalMinutes = $rules->total_time_minutes ?? $rules->expires_after_join_minutes;
        $participation->attempt_expires_at = $totalMinutes
            ? now()->addMinutes((int) $totalMinutes)
            : null;
        $participation->save();

        NotificationService::triviaStarted($user, $contest);

        TriviaProgress::updateOrCreate(
            ['contest_id' => $contest->id, 'user_id' => $user->id],
            [
                'current_order' => 1,
                'question_started_at' => now(),
                'finished' => false,
            ]
        );

        return redirect()->route('user.trivia.play', $contest);
    }

    public function play(Contest $contest)
    {
        $user = auth()->user();
        $rules = $contest->rules;

        if ($resp = $this->guardAllowedForUser($contest)) {
            return $resp;
        }

        $participation = Participation::where('contest_id', $contest->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Regla: tiempo límite total
        if ($participation->attempt_expires_at && now()->gt($participation->attempt_expires_at)) {
            abort(403, 'Se terminó tu tiempo para responder la trivia');
        }

        $progress = TriviaProgress::where('contest_id', $contest->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($progress->finished) {
            return redirect()->route('user.trivia.result', $contest);
        }

        $hasSegmentedQuestions = $contest->questions()->where('audience', '!=', 'all')->exists();
        if ($hasSegmentedQuestions && !$user->birthdate) {
            return $this->redirectNeedBirthdate('Necesitas registrar tu fecha de nacimiento para ver preguntas por edad.');
        }

        $allowedAudiences = $this->allowedQuestionAudiences($contest, $user);

        $originalOrder = (int) $progress->current_order;

        $question = $contest->questions()
            ->where('order', '>=', $originalOrder)
            ->whereIn('audience', $allowedAudiences)
            ->orderBy('order')
            ->orderBy('id')
            ->first();

        if ($question && (int) $question->order !== $originalOrder) {
            $progress->current_order = (int) $question->order;
            $progress->question_started_at = now();
            $progress->save();
        }

        if (!$question) {
            $progress->finished = true;
            $progress->save();
            return redirect()->route('user.trivia.result', $contest);
        }

        return view('trivia.play', compact('contest', 'question', 'rules', 'progress', 'participation'));
    }

    public function timeout(Request $request, Contest $contest)
    {
        $user = auth()->user();
        $rules = $contest->rules;

        $this->guardAllowedForUser($contest);

        $participation = Participation::where('contest_id', $contest->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Regla: tiempo límite total
        if ($participation->attempt_expires_at && now()->gt($participation->attempt_expires_at)) {
            abort(403, 'Se terminó tu tiempo para responder la trivia');
        }

        $progress = TriviaProgress::where('contest_id', $contest->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($progress->finished) {
            return redirect()->route('user.trivia.result', $contest);
        }

        $progress->current_order += 1;
        $progress->question_started_at = now();
        $progress->save();

        return redirect()
            ->route('user.trivia.play', $contest)
            ->with('error', '⏱️ Tiempo agotado. Pasaste automáticamente a la siguiente pregunta.');
    }

    public function answer(Request $request, Contest $contest)
    {
        $request->validate([
            'question_id' => 'required|integer',
            'selected_option' => 'required|in:a,b,c,d',
        ]);

        $user = auth()->user();
        $rules = $contest->rules;

        $this->guardAllowedForUser($contest);

        $participation = Participation::where('contest_id', $contest->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Regla: tiempo límite total
        if ($participation->attempt_expires_at && now()->gt($participation->attempt_expires_at)) {
            abort(403, 'Se terminó tu tiempo para responder la trivia');
        }

        $progress = TriviaProgress::where('contest_id', $contest->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($rules->seconds_per_question && $progress->question_started_at) {
            $limit = $progress->question_started_at->copy()->addSeconds($rules->seconds_per_question);

            if (now()->gt($limit)) {
                $progress->current_order += 1;
                $progress->question_started_at = now();
                $progress->save();

                return redirect()->route('user.trivia.play', $contest)
                    ->with('error', 'Tiempo agotado. Pasaste a la siguiente pregunta.');
            }
        }

        $allowedAudiences = $this->allowedQuestionAudiences($contest, $user);

        $question = $contest->questions()
            ->where('id', $request->question_id)
            ->whereIn('audience', $allowedAudiences)
            ->firstOrFail();
        abort_unless($question->order === $progress->current_order, 403, 'Pregunta inválida');

        $selectedOption = strtoupper(trim($request->selected_option));
        $correctOption = strtoupper(trim($question->correct_option));
        $isCorrect = ($selectedOption === $correctOption);

        Answer::updateOrCreate(
            [
                'contest_id' => $contest->id,
                'contest_question_id' => $question->id,
                'user_id' => $user->id,
            ],
            [
                'selected_option' => $request->selected_option,
                'is_correct' => $isCorrect,
                'answered_at' => now(),
            ]
        );

        $progress->current_order += 1;
        $progress->question_started_at = now();
        $progress->save();

        return redirect()->route('user.trivia.play', $contest);
    }

    public function result(Contest $contest)
    {
$user = auth()->user();

abort_unless($contest->type === 'trivia', 404);
$this->guardAllowedForUser($contest);

$total = $contest->questions()->count();

$correct = Answer::where('contest_id', $contest->id)
    ->where('user_id', $user->id)
    ->where('is_correct', true)
    ->count();

$wrong = max(0, (int) $total - (int) $correct);

$participation = Participation::where('contest_id', $contest->id)
    ->where('user_id', $user->id)
    ->first();

abort_unless($participation, 403, 'No tienes participación en esta trivia.');

if ((int) $participation->last_result_attempt < (int) $participation->attempts_used) {

    $finishedAt = now();
    $durationSeconds = $participation->attempt_started_at
        ? $participation->attempt_started_at->diffInSeconds($finishedAt)
        : null;

    $participation->last_correct = (int) $correct;
    $participation->last_wrong = (int) $wrong;
    $participation->last_duration_seconds = $durationSeconds;
    $participation->last_finished_at = $finishedAt;
    $participation->last_result_attempt = (int) $participation->attempts_used;
    // Marcar como finalizado para que entre al ranking/publicación de ganadores
    $participation->status = 'finished';
    $participation->save();

    NotificationService::triviaCompleted($user, $contest, (int) $correct, (int) $total, (int) $wrong, $durationSeconds);

    $leader = Participation::where('contest_id', $contest->id)
        ->whereNotNull('last_finished_at')
        ->orderByDesc('last_correct')
        ->orderBy('last_duration_seconds')
        ->orderBy('last_finished_at')
        ->with('user')
        ->first();

    $company = User::find($contest->company_id);
    if ($company) {
        $leaderData = null;

        if ($leader && $leader->user) {
            $leaderData = [
                'name' => $leader->user->name ?: $leader->user->email,
                'correct' => (int) $leader->last_correct,
                'duration' => $leader->last_duration_seconds,
            ];
        }

        NotificationService::triviaResultToCompany($company, $user, $contest, (int) $correct, (int) $total, (int) $wrong, $durationSeconds, $leaderData);

        if ($leader && $leader->user_id === $user->id && ($contest->winner_method ?? 'ranking') === 'ranking') {
            User::adminsQuery()->get()->each(function ($admin) use ($user, $contest, $correct, $total, $durationSeconds) {
                NotificationService::send(
                    $admin,
                    '🏆 Nuevo líder en trivia',
                    'Usuario "' . ($user->name ?: $user->email) . '" lidera "' . $contest->title . '". Aciertos: ' . $correct . '/' . $total . ($durationSeconds ? ' | Tiempo: ' . $durationSeconds . 's' : ''),
                    route('admin.contests_adv.index')
                );
            });
        }
    }

    $participation->last_notified_attempt = (int) $participation->attempts_used;
    $participation->save();
}

$duration_seconds = $participation->last_duration_seconds;

$leader = Participation::where('contest_id', $contest->id)
    ->whereNotNull('last_finished_at')
    ->orderByDesc('last_correct')
    ->orderBy('last_duration_seconds')
    ->orderBy('last_finished_at')
    ->with('user')
    ->first();

return view('trivia.result', compact('contest', 'total', 'correct', 'wrong', 'duration_seconds', 'leader'));
    }
}
