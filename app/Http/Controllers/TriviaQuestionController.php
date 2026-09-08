<?php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesTriviaArea;
use App\Models\Contest;
use App\Models\ContestQuestion;
use Illuminate\Http\Request;

class TriviaQuestionController extends Controller
{
    use HandlesTriviaArea;
    public function index(Request $request, Contest $contest)
    {
        $area = $request->route()->defaults['area'] ?? $request->route('area', 'company');
        $contest = $this->guardTriviaOwner($contest);

        $questions = ContestQuestion::where('contest_id', $contest->id)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view($this->viewBase($area) . '.trivia.questions.index', compact('contest', 'questions'));
    }

    public function create(Request $request, Contest $contest)
    {
        $area = $request->route()->defaults['area'] ?? $request->route('area', 'company');
        $contest = $this->guardTriviaOwner($contest);

        if (!config('ganafacil.trivia_manual_questions_enabled', true)) {
            return redirect()
                ->route($this->routeBase($area) . 'trivia.questions.index', $contest)
                ->with('error', '⚠️ La creación manual de preguntas está deshabilitada. Usa Importar Excel/CSV.');
        }

        return view($this->viewBase($area) . '.trivia.questions.create', compact('contest'));
    }

    public function store(Request $request, Contest $contest)
    {
        $area = $request->route()->defaults['area'] ?? $request->route('area', 'company');
        $contest = $this->guardTriviaOwner($contest);

        if (!config('ganafacil.trivia_manual_questions_enabled', true)) {
            return back()->with('error', '⚠️ La creación manual de preguntas está deshabilitada. Usa Importar Excel/CSV.');
        }

        $data = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
            'option_a' => ['required', 'string', 'max:255'],
            'option_b' => ['required', 'string', 'max:255'],
            'option_c' => ['nullable', 'string', 'max:255'],
            'option_d' => ['nullable', 'string', 'max:255'],
            'correct_option' => ['required', 'in:A,B,C,D,a,b,c,d'],
            'audience' => ['required', 'in:all,adult,kid'],
        ]);

        $maxOrder = (int) ContestQuestion::where('contest_id', $contest->id)->max('order');

        ContestQuestion::create([
            'contest_id' => $contest->id,
            'question' => $data['question'],
            'option_a' => $data['option_a'],
            'option_b' => $data['option_b'],
            'option_c' => $data['option_c'] ?? null,
            'option_d' => $data['option_d'] ?? null,
            'correct_option' => strtoupper($data['correct_option']),
            'audience' => $data['audience'],
            'order' => $maxOrder + 1,
        ]);
        return redirect()
            ->route($this->routeBase($area) . 'trivia.questions.index', $contest)
            ->with('success', '✅ Pregunta agregada correctamente.');
    }

    public function destroy(Request $request, Contest $contest, ContestQuestion $question)
    {
        $area = $request->route()->defaults['area'] ?? $request->route('area', 'company');
        $contest = $this->guardTriviaOwner($contest);

        if ((int) $question->contest_id !== (int) $contest->id) {
            abort(404);
        }

        $question->delete();

        return back()->with('success', '🗑️ Pregunta eliminada.');
    }
}
