<?php
namespace App\Http\Controllers;

use App\Models\Contest;
use App\Models\User;
use App\Notifications\DbNotification;
use App\Services\NotificationService;
use App\Services\ContestWinnerService;
use App\Services\CompanyService;
use App\Services\ContestAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ContestBaseController extends Controller
{
    public function publishWinnersManual(Request $request, Contest $contest)
    {
        $area = $this->getAreaName($request);
        if ($area === 'company') {
            $company = $request->user();
            if ((int) $contest->company_id !== (int) $company->id) {
                abort(403);
            }
        }
        $result = app(\App\Services\ContestWinnerService::class)->publishWinners($contest);
        if (!($result['ok'] ?? false)) {
            return back()->with('error', (string) ($result['message'] ?? 'No se pudo publicar ganadores.'));
        }
        return back()->with('success', (string) ($result['message'] ?? '✅ Ganadores publicados correctamente.'));
    }
    protected function getAreaName(Request $request)
    {
        $path = $request->route()->getPrefix();
        if (Str::contains($path, 'admin')) return 'admin';
        if (Str::contains($path, 'moderator')) return 'moderator';
        if (Str::contains($path, 'company')) return 'company';
        return 'admin';
    }

    protected function getRedirectRoute($area, $action, $contest = null)
    {
        $routes = [
            'admin' => [
                'index' => 'admin.contests_adv.index',
                'questions' => 'admin.trivia.questions.index',
                'rules' => 'admin.trivia.rules.edit',
            ],
            'moderator' => [
                'index' => 'moderator.contests_adv.index',
                'questions' => 'moderator.trivia.questions.index',
                'rules' => 'moderator.trivia.rules.edit',
            ],
            'company' => [
                'index' => 'company.contests.index',
                'questions' => 'company.trivia.questions.index',
                'rules' => 'company.trivia.rules.edit',
            ],
        ];

        $route = $routes[$area][$action] ?? $routes['admin'][$action];
        return $contest ? route($route, $contest) : route($route);
    }

    public function index(Request $request)
    {
        $area = $this->getAreaName($request);

        if ($area === 'company') {
            $company = $request->user();
            $contests = Contest::where('company_id', $company->id)
                ->with(['rules', 'prizes'])
                ->withCount('questions')
                ->latest('id')
                ->get();
        } else {
            $contests = Contest::with(['company:id,name,company_name,razon_social', 'rules', 'prizes'])
                ->orderByDesc('id')
                ->get();
        }

        $viewMap = [
            'admin' => 'admin.contests_adv.index',
            'moderator' => 'moderator.contests_adv.index',
            'company' => 'company.contests.index',
        ];

        return view($viewMap[$area], compact('contests'));
    }

    public function create(Request $request)
    {
        $area = $this->getAreaName($request);

        if ($area === 'company') {
            return view('contests.create');
        }

        $companies = CompanyService::getCompanies()
            ->get(['id', 'name', 'company_name', 'razon_social']);

        return view('contests.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $area = $this->getAreaName($request);

        if ($area === 'company') {
            return $this->storeCompany($request);
        }

        return $this->storeAdminModerator($request, $area);
    }

    protected function storeCompany(Request $request)
    {
        $company = $request->user();

        $data = $request->validate([
            'type' => ['required', 'in:trivia'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quick_close_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'end_at' => ['nullable', 'required_without:quick_close_minutes', 'date_format:Y-m-d\TH:i', 'after:now'],
        ], [
            'end_at.required_without' => '⚠️ Debes elegir la fecha y hora de cierre (o seleccionar un cierre rápido).',
            'end_at.date_format' => '⚠️ La fecha/hora de cierre no es válida.',
            'end_at.after' => '⚠️ La fecha/hora de cierre debe ser futura.',
        ]);

        $startAt = Carbon::now();
        if (!empty($data['quick_close_minutes'])) {
            $endAt = $startAt->copy()->addMinutes((int) $data['quick_close_minutes']);
        } else {
            $endAt = Carbon::createFromFormat('Y-m-d\TH:i', $data['end_at']);
        }

        $contest = Contest::create([
            'company_id' => $company->id,
            'type' => ($data['type'] ?? 'trivia'),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_at' => Carbon::now(),
            'end_at' => $endAt,
            'status' => 'draft',
            'winner_method' => 'ranking',
            'draw_enabled' => false,
            'draw_top_n' => 10,
        ]);

        NotificationService::send($company, '✅ Trivia creada', 'Se creó la trivia "' . $contest->title . '" (Paso 1/2). Ahora puedes cargar preguntas.', route('company.trivia.questions.index', $contest));

        User::adminsQuery()->get()->each(function ($a) use ($company, $contest) {
            NotificationService::send($a, '🧩 Nueva trivia creada (borrador)', 'La empresa "' . ($company->company_name ?? $company->name) . '" creó la trivia "' . $contest->title . '" (Paso 1/2).', route('admin.contests_adv.index'));
        });

        return redirect()
            ->route('company.trivia.questions.index', $contest)
            ->with('success', '✅ Trivia creada (Paso 1/2). Ahora carga las preguntas.');
    }

    protected function storeAdminModerator(Request $request, $area)
    {
        $data = $request->validate([
            'company_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'company')],
            'type' => ['required', 'in:trivia'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quick_close_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'end_at' => ['nullable', 'required_without:quick_close_minutes', 'date_format:Y-m-d\TH:i', 'after_or_equal:now'],
        ], [
            'end_at.required_without' => '⚠️ Debes elegir la fecha y hora de cierre (o seleccionar un cierre rápido).',
            'end_at.date_format' => '⚠️ La fecha/hora de cierre no es válida.',
            'end_at.after_or_equal' => '⚠️ La fecha/hora de cierre no puede ser pasada.',
        ]);

        $company = User::where('id', (int) $data['company_id'])->where('role', 'company')->first();
        if (!$company) {
            return back()->withInput()->with('error', '⚠️ Debes seleccionar una empresa válida.');
        }

        try {
            if (!empty($data['quick_close_minutes'])) {
                $endAt = now()->addMinutes((int) $data['quick_close_minutes']);
            } else {
                $endAt = Carbon::createFromFormat('Y-m-d\TH:i', $data['end_at']);
            }
        } catch (\Exception $e) {
            return back()->withInput()->with('error', '⚠️ Las fechas ingresadas no son válidas. Revise el calendario.');
        }

        if ($endAt->lte(now())) {
            return back()->withInput()->with('error', '⚠️ La fecha/hora de cierre debe ser posterior a la fecha actual.');
        }

        $contest = Contest::create([
            'company_id' => (int) $company->id,
            'type' => 'trivia',
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_at' => now(),
            'end_at' => $endAt,
            'draw_at' => null,
            'status' => 'draft',
            'winner_method' => 'ranking',
            'draw_enabled' => false,
            'draw_top_n' => 10,
        ]);

        $redirectRoute = $area === 'admin'
            ? route('admin.trivia.questions.index', $contest)
            : route('moderator.trivia.questions.index', $contest);

        return redirect($redirectRoute)
            ->with('success', '✅ Trivia creada (Paso 1/2). Ahora carga las preguntas.');
    }

    public function publish(Request $request, Contest $contest)
    {
        $area = $this->getAreaName($request);

        if ($area === 'company') {
            return $this->publishCompany($request, $contest);
        }

        return $this->publishAdminModerator($request, $contest, $area);
    }

    protected function publishCompany(Request $request, Contest $contest)
    {
        $company = $request->user();

        try {
            ContestAccessService::validateOwnership($contest, $company, 'company');
        } catch (\Exception $e) {
            return redirect()->route('company.contests.index')->with('error', $e->getMessage());
        }

        if ((int) ($company->voucher_approved ?? 0) !== 1) {
            return redirect()
                ->route('company.subscription')
                ->with('error', '⚠️ Debes subir tu comprobante de pago y esperar aprobación para publicar.');
        }

        if (($contest->status ?? 'draft') !== 'draft') {
            return back()->with('error', 'Este concurso ya está publicado o no puede publicarse.');
        }

        if (!empty($contest->end_at) && now()->gt($contest->end_at)) {
            return back()->with('error', '⚠️ La fecha de cierre ya pasó. Edita y coloca una fecha futura.');
        }

        if (($contest->type ?? null) === 'trivia') {
            $check = $contest->validatePublishRules();
            if (!($check['ok'] ?? false)) {
                $msg = (string) ($check['message'] ?? '⚠️ No se puede publicar.');

                if (Str::contains($msg, 'Paso 2') || Str::contains($msg, 'premio')) {
                    return redirect()
                        ->route('company.trivia.rules.edit', $contest)
                        ->with('error', $msg);
                }

                return back()->with('error', $msg);
            }
        }

        $contest->update([
            'status' => 'active',
            'start_at' => $contest->start_at ?: now(),
        ]);

        NotificationService::send($company, '🚀 Concurso publicado', 'Tu concurso "' . $contest->title . '" ya está activo.', route('company.contests.index'));

        User::adminsQuery()->get()->each(function ($a) use ($company, $contest) {
            NotificationService::send($a, '🚀 Concurso publicado', 'La empresa "' . ($company->company_name ?? $company->name) . '" publicó "' . $contest->title . '".', route('admin.contests_adv.index'));
        });

        User::where('role', 'user')->where('active', 1)->chunk(500, function ($chunk) use ($contest) {
            foreach ($chunk as $u) {
                NotificationService::send($u, '🎉 Nuevo concurso disponible', "Ya puedes participar en: {$contest->title}", route('user.contests.list'));
            }
        });

        return back()->with('success', '✅ Concurso publicado correctamente.');
    }

    protected function publishAdminModerator(Request $request, Contest $contest, $area)
    {
        if (($contest->type ?? null) !== 'trivia') {
            return back()->with('error', '⚠️ Solo se pueden publicar trivias.');
        }

        if (($contest->status ?? null) !== 'draft') {
            return back()->with('error', 'Este concurso ya está publicado o no puede publicarse.');
        }

        if ($contest->end_at && Carbon::parse($contest->end_at)->isPast()) {
            return back()->with('error', '⚠️ La fecha de cierre ya pasó. Ajusta la fecha antes de publicar.');
        }

        $check = $contest->validatePublishRules();
        if (!($check['ok'] ?? false)) {
            $msg = (string) ($check['message'] ?? '⚠️ No se puede publicar.');
            if (Str::contains($msg, 'Paso 2') || Str::contains($msg, 'premio')) {
                $redirectRoute = $area === 'admin'
                    ? route('admin.trivia.rules.edit', $contest)
                    : route('moderator.trivia.rules.edit', $contest);
                return redirect($redirectRoute)->with('error', $msg);
            }
            return back()->with('error', $msg);
        }

        $contest->update(['status' => 'active']);

        User::where('role', 'user')->where('active', 1)->chunk(500, function ($chunk) use ($contest) {
            foreach ($chunk as $u) {
                NotificationService::send($u, '🎉 Nueva trivia disponible', "Ya puedes participar en: {$contest->title}", route('user.contests.list'));
            }
        });

        return back()->with('success', '✅ Trivia publicada. Ya es visible para los usuarios.');
    }

    public function publishWinners(Request $request, Contest $contest)
    {
        $area = $this->getAreaName($request);

        if ($area === 'company') {
            $company = $request->user();
            if ((int) $contest->company_id !== (int) $company->id) {
                abort(403);
            }
        }

        // Aplicar reglas guardadas del concurso. Si en ...
        $options = [
            'winners_count' => (int)($contest->rules?->winners_count ?? ($request->input('winners_count') ?? 1)),
            'eligible_top_n' => (int)($contest->rules?->eligible_top_n ?? ($request->input('eligible_top_n') ?? 0)),
        ];

        $result = app(ContestWinnerService::class)->publish($contest, $options);

        if (!($result['ok'] ?? false)) {
            return back()->with('error', (string) ($result['message'] ?? 'No se pudo publicar.'));
        }

        return back()->with('success', (string) ($result['message'] ?? '✅ Ganadores publicados correctamente.'));
    }
}
