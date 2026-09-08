<?php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesTriviaArea;
use App\Models\Contest;
use App\Models\ContestQuestion;
use App\Models\ContestRule;
use App\Models\Prize;
use Illuminate\Http\Request;

class TriviaRulesController extends Controller
{
    use HandlesTriviaArea;
    public function edit(Request $request, Contest $contest)
    {
        $area = $request->route()->defaults['area'] ?? $request->route('area', 'company');
        $contest = $this->guardTriviaOwner($contest);

        $questionsCount = (int) ContestQuestion::where('contest_id', $contest->id)->count();
        if ($questionsCount < 1) {
            return redirect()
                ->route($this->routeBase($area) . 'trivia.questions.index', $contest)
                ->with('error', '⚠️ Primero carga preguntas (Paso 1) antes de configurar reglas.');
        }

        $rule = ContestRule::where('contest_id', $contest->id)->first();

        $mainPrize = Prize::where('contest_id', $contest->id)
            ->whereIn('kind', ['ranking','lottery'])
            ->orderBy('position')
            ->first();

        $drawPrize = Prize::where('contest_id', $contest->id)
            ->where('kind', 'draw')
            ->first();

        return view($this->viewBase($area) . '.trivia.rules.edit', [
            'contest' => $contest,
            'rule' => $rule,
            'questionsCount' => $questionsCount,
            'mainPrize' => $mainPrize,
            'drawPrize' => $drawPrize,
        ]);
    }

    public function update(Request $request, Contest $contest)
    {
        $area = $request->route()->defaults['area'] ?? $request->route('area', 'company');
        $contest = $this->guardTriviaOwner($contest);

        $questionsCount = (int) ContestQuestion::where('contest_id', $contest->id)->count();
        if ($questionsCount < 1) {
            return redirect()
                ->route($this->routeBase($area) . 'trivia.questions.index', $contest)
                ->with('error', '⚠️ Primero carga preguntas (Paso 1) antes de configurar reglas.');
        }

        $data = $request->validate([
            'seconds_per_question' => ['required','integer','min:1','max:600'],
            'warning_seconds' => ['nullable','integer','min:0','max:600'],
            'attempts' => ['required','integer','min:1','max:10'],
            'total_time_minutes' => ['nullable','integer','min:1','max:240'],
            'age_group' => ['required','in:all,kids,teens,adults'],
            'winner_method' => ['required','in:ranking,lottery'],
            'winners_count' => ['nullable','integer','min:1','max:100'],
            'eligible_scope' => ['nullable','in:all,top'],
            'eligible_top_n' => ['nullable','integer','min:1','max:500'],
            'prize_name' => ['required','string','max:255'],
            'prize_quantity' => ['nullable','integer','min:1','max:1000'],
            'prize_image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:10240'],
        ]);

        $winnerMethod = $data['winner_method'];

        $secondsPerQuestion = (int) $data['seconds_per_question'];
        $qCount = $questionsCount;
        $triviaTotalSeconds = $secondsPerQuestion * $qCount;

        $eligibleScope = $data['eligible_scope'] ?? 'all';
        $eligibleTopN = ($winnerMethod === 'lottery' && $eligibleScope === 'top')
            ? (int)($data['eligible_top_n'] ?? 10)
            : 0;

        $winnersCount = (int) ($data['winners_count'] ?? 1);
        $winnersCount = max(1, min(100, $winnersCount));

        ContestRule::updateOrCreate(
            ['contest_id' => $contest->id],
            [
                'seconds_per_question' => $secondsPerQuestion,
                'warning_seconds' => (int)($data['warning_seconds'] ?? 0),
                'trivia_questions_count' => $qCount,
                'trivia_total_seconds' => $triviaTotalSeconds,
                'attempts' => (int) $data['attempts'],
                'total_time_minutes' => !empty($data['total_time_minutes']) ? (int)$data['total_time_minutes'] : null,
                'age_group' => $data['age_group'],
                'eligible_top_n' => $eligibleTopN,
                'winners_count' => $winnersCount,
            ]
        );

        $contest->update([
            'winner_method' => $winnerMethod,
        ]);

        $existingMain = Prize::where('contest_id', $contest->id)
            ->whereIn('kind', ['ranking','lottery'])
            ->orderBy('position')
            ->first();

        $mainImagePath = $existingMain?->image_path;

        if ($request->hasFile('prize_image')) {
            $mainImagePath = $request->file('prize_image')->store('prizes', 'public');
        }

        $qty = (int) ($data['prize_quantity'] ?? 1);

        Prize::where('contest_id', $contest->id)->whereIn('kind', ['ranking','lottery'])->delete();

        if ($winnerMethod === 'ranking') {
            for ($pos = 1; $pos <= $winnersCount; $pos++) {
                Prize::create([
                    'contest_id' => $contest->id,
                    'kind' => 'ranking',
                    'name' => $data['prize_name'],
                    'quantity' => $qty,
                    'image_path' => $mainImagePath,
                    'position' => $pos,
                ]);
            }
        } else {
            for ($pos = 1; $pos <= $winnersCount; $pos++) {
                Prize::create([
                    'contest_id' => $contest->id,
                    'kind' => 'lottery',
                    'name' => $data['prize_name'],
                    'quantity' => $qty,
                    'image_path' => $mainImagePath,
                    'position' => $pos,
                ]);
            }
        }

        $area = $request->route()->defaults['area'] ?? $request->route('area', 'company');

        $check = $contest->validatePublishRules();
        if (!($check['ok'] ?? false)) {
            return redirect()
                ->route($this->routeBase($area) . 'trivia.questions.index', $contest)
                ->with('error', $check['message'] ?? '⚠️ Debes completar todos los pasos antes de publicar.');
        }

        $routeName = $area === 'admin' ? 'admin.contests_adv.index' : ($area === 'moderator' ? 'moderator.contests.index' : 'company.contests.index');

        return redirect()
            ->route($routeName)
            ->with('success', '✅ Paso 2 completado. Todo listo. Ya puedes publicar la trivia.');
    }
}

