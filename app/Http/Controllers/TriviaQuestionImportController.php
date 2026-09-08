<?php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesTriviaArea;
use App\Models\Contest;
use App\Models\ContestQuestion;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Services\TriviaImportService;
use App\Services\CompanyService;

class TriviaQuestionImportController extends Controller
{
    use HandlesTriviaArea;

    public function form(Request $request, Contest $contest)
    {
        $area = $request->route()->defaults['area'] ?? $request->route('area', 'company');
        $contest = $this->guardTriviaOwner($contest);

        $companies = null;
        if ($area === 'admin' && empty($contest->company_id)) {
            $companies = CompanyService::getCompanies()
                ->get(['id','name','company_name','razon_social']);
        }

        $routeBase = $this->routeBase($area);

        return view('shared.trivia.questions.import', compact('contest','companies','routeBase'));
    }

    public function template(Request $request, Contest $contest)
    {
        $area = $request->route()->defaults['area'] ?? $request->route('area', 'company');
        $contest = $this->guardTriviaOwner($contest);

        $headers = ['question','option_a','option_b','option_c','option_d','correct_option'];

        $sample = [
            '¿Cuánto es 2 + 2?',
            '3',
            '4',
            '5',
            '6',
            'B',
        ];

        $content = TriviaImportService::csvLine($headers) . TriviaImportService::csvLine($sample);

        return response($content)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="plantilla_trivia_'.$contest->id.'.csv"');
    }

    public function import(Request $request, Contest $contest)
    {
        $area = $request->route()->defaults['area'] ?? $request->route('area', 'company');
        $contest = $this->guardTriviaOwner($contest);

        if ($area === 'admin' && empty($contest->company_id)) {
            $request->validate([
                'company_id' => ['required','integer','exists:users,id'],
            ]);

            $company = User::where('id', (int)$request->company_id)->where('role','company')->first();
            if (!$company) {
                return back()->withInput()->with('error', '⚠️ Debes seleccionar una empresa válida.');
            }

            $contest->company_id = (int) $company->id;
            $contest->save();
        }

        $request->validate([
            'file' => ['required','file','mimes:csv,txt,xlsx,xls'],
        ]);

        $ext = strtolower($request->file('file')->getClientOriginalExtension());

        $payloads = in_array($ext, ['xlsx','xls'], true)
            ? TriviaImportService::parseExcel($request->file('file'))
            : TriviaImportService::parseCsv($request->file('file')->getRealPath());

        $valid = [];
        foreach ($payloads as $p) {
            if (is_array($p) && TriviaImportService::isValid($p)) {
                $valid[] = TriviaImportService::normalize($p);
            }
        }

        if (!$valid) {
            return back()->with('error', 'No se encontraron preguntas válidas.');
        }

        session()->put("trivia_import_{$contest->id}", $valid);

        return redirect()
            ->route($this->routeBase($area) . 'trivia.questions.import.select', $contest)
            ->with('success', 'Archivo cargado correctamente.');
    }

    public function select(Request $request, Contest $contest)
    {
        $area = $request->route()->defaults['area'] ?? $request->route('area', 'company');
        $contest = $this->guardTriviaOwner($contest);

        $payloads = session()->get("trivia_import_{$contest->id}");

        if (!$payloads) {
            return redirect()
                ->route($this->routeBase($area) . 'trivia.questions.import.form', $contest)
                ->with('error', 'No hay archivo cargado.');
        }

        $routeBase = $this->routeBase($area);

        return view('shared.trivia.questions.import_select', compact('contest','payloads','routeBase'));
    }

    public function commit(Request $request, Contest $contest)
    {
        $area = $request->route()->defaults['area'] ?? $request->route('area', 'company');
        $contest = $this->guardTriviaOwner($contest);

        $payloads = session()->get("trivia_import_{$contest->id}");

        if (!$payloads) {
            return redirect()
                ->route($this->routeBase($area) . 'trivia.questions.import.form', $contest)
                ->with('error', 'La sesión expiró.');
        }

        $request->validate([
            'selected' => ['required','array'],
            'selected.*' => ['integer'],
        ]);

        $maxOrder = (int) ContestQuestion::where('contest_id', $contest->id)->max('order');
        $created = 0;

        foreach ($request->selected as $i) {
            if (!isset($payloads[$i])) continue;

            $p = $payloads[$i];

            ContestQuestion::create([
                'contest_id' => $contest->id,
                'question' => $p['question'],
                'option_a' => $p['option_a'],
                'option_b' => $p['option_b'],
                'option_c' => $p['option_c'],
                'option_d' => $p['option_d'],
                'correct_option' => $p['correct_option'],
                'audience' => $p['audience'] ?? 'all',
                'order' => ++$maxOrder,
            ]);

            $created++;
        }

        $company = $contest->company_id ? User::find((int)$contest->company_id) : null;
        if ($company && $company->role === 'company') {
            NotificationService::send($company, '📥 Preguntas importadas', 'Se importaron ' . (int)$created . ' preguntas en la trivia "' . $contest->title . '".', route('company.trivia.questions.index', $contest));
        }

        session()->forget("trivia_import_{$contest->id}");

        return redirect()
            ->route($this->routeBase($area) . 'trivia.questions.index', $contest)
            ->with('success', "✅ {$created} preguntas importadas.");
    }

    private function csvLine(array $fields): string
    {
        return TriviaImportService::csvLine($fields);
    }
}

