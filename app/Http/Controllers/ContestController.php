<?php

namespace App\Http\Controllers;

use App\Models\Contest;
use Illuminate\Http\Request;
use App\Rules\ValidCalendarDate;
use App\Rules\ReasonableDate;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Notifications\DbNotification;

class ContestController extends Controller
{

    public function publish($id) {
        $contest = Contest::find($id);
        if ($contest) {
            $contest->published = true;
            $contest->save();
            return redirect()->back()->with('success', 'El concurso ha sido publicado.');
        }
        return redirect()->back()->with('error', 'Concurso no encontrado.');
    }

    public function calculateResults($contestId) {
        $contest = Contest::find($contestId);
        if ($contest) {
        }
    }

    public function setWinners(Request $request, $contestId) {
        $contest = Contest::find($contestId);
        if ($contest) {
            $numberOfWinners = $request->input('number_of_winners');
            return redirect()->back()->with('success', 'Ganadores establecidos.');
        }
        return redirect()->back()->with('error', 'Concurso no encontrado.');
    }
    public function index()
    {
        $contests = Contest::with('company:id,name')
            ->orderByDesc('id')
            ->get();

        return view('admin.contests.index', compact('contests'));
    }

    public function create()
    {
        $companies = User::where('role','company')->orderBy('name')->get(['id','name']);
        return view('admin.contests.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => ['required','exists:users,id'],
            'type' => ['required','in:trivia'],
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],

            'start_date' => ['required','date_format:Y-m-d', new ValidCalendarDate, new ReasonableDate, 'after_or_equal:today'],
            'end_date' => ['required','date_format:Y-m-d', new ValidCalendarDate, new ReasonableDate, 'after:start_date'],

            'status' => ['required','in:draft,active,ended,cancelled,finished'],
        ], [
            'start_date.date_format' => '⚠️ Las fechas ingresadas no son válidas. Revise el calendario.',
            'start_date.after_or_equal' => '⚠️ No se puede crear un concurso con una fecha pasada.',
            'end_date.date_format' => '⚠️ Las fechas ingresadas no son válidas. Revise el calendario.',
            'end_date.after' => '⚠️ Si existe fecha de inicio, la fecha de fin debe ser posterior a la fecha de inicio.',
        ]);

        // Compatibilidad: si la vista vieja envía "finished", lo tratamos como "ended".
        if (($data['status'] ?? null) === 'finished') {
            $data['status'] = 'ended';
        }



        $startAt = Carbon::createFromFormat('Y-m-d', $data['start_date'])->startOfDay();
        $endAt   = Carbon::createFromFormat('Y-m-d', $data['end_date'])->endOfDay();

        if ($endAt->lte(now())) {
            return back()->withInput()->with('error', '⚠️ La fecha de finalización debe ser posterior a la fecha actual.');
        }

        Contest::create([
            'company_id' => $data['company_id'],
            'type' => $data['type'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => $data['status'],
        ]);

        return redirect()->route('admin.contests.index')->with('success', 'El concurso fue creado correctamente.');
    }

    public function edit($id)
    {
        $contest = Contest::findOrFail($id);
        return view('admin.contests.edit', compact('contest'));
    }

    public function update(Request $request, $id)
    {
        $contest = Contest::findOrFail($id);

        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],

            'start_date' => ['required','date_format:Y-m-d', new ValidCalendarDate, new ReasonableDate, 'after_or_equal:today'],
            'end_date' => ['required','date_format:Y-m-d', new ValidCalendarDate, new ReasonableDate, 'after:start_date'],

            'status' => ['required','in:draft,active,ended,cancelled'],
        ], [
            'start_date.date_format' => '⚠️ Las fechas ingresadas no son válidas. Revise el calendario.',
            'start_date.after_or_equal' => '⚠️ No se puede crear un concurso con una fecha pasada.',
            'end_date.date_format' => '⚠️ Las fechas ingresadas no son válidas. Revise el calendario.',
            'end_date.after' => '⚠️ Si existe fecha de inicio, la fecha de fin debe ser posterior a la fecha de inicio.',
        ]);

        // Compatibilidad: si la vista vieja envía "finished", lo tratamos como "ended".
        if (($data['status'] ?? null) === 'finished') {
            $data['status'] = 'ended';
        }



        $startAt = Carbon::createFromFormat('Y-m-d', $data['start_date'])->startOfDay();
        $endAt   = Carbon::createFromFormat('Y-m-d', $data['end_date'])->endOfDay();

        if ($endAt->lte(now())) {
            return back()->withInput()->with('error', '⚠️ La fecha de finalización debe ser posterior a la fecha actual.');
        }

        $contest->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => $data['status'],
        ]);

        return redirect()->route('admin.contests.index')->with('success','La información fue actualizada.');
    }

    public function destroy($id)
    {
        Contest::findOrFail($id)->delete();
        return back()->with('success','Eliminado.');
    }

    public function list()
    {
        $user = auth()->user();

        $needsBirthdateForAge = empty($user?->birthdate) && Contest::where('status', "active")
            ->whereHas('rules', function ($q) {
                $q->where('only_adults', 1)
                  ->orWhere('age_group', "!=", "all");
            })->exists();

        if ($needsBirthdateForAge && $user) {
            $alreadyNotified = $user->notifications()
                ->where('type', DbNotification::class)
                ->where('data->key', 'complete_profile_birthdate')
                ->where('created_at', '>=', now()->subDay())
                ->exists();

            if (!$alreadyNotified) {
                $user->notify(new DbNotification([
                    'key' => 'complete_profile_birthdate',
                    'title' => 'Completa tu perfil',
                    'message' => 'Registra tu fecha de nacimiento para calcular tu edad y mostrarte concursos adecuadas (por ejemplo 18+).',
                    'url' => route('profile.edit'),
                ]));
            }
        }

        $contests = Contest::with(['company:id,name', 'rules', 'prizes'])->withCount('questions')
            ->where('status', 'active')
            ->orderByDesc('id')
            ->get();

        $age = null;
        if ($user?->birthdate) {
            $age = $user->birthdate->age;
        }

        $contests = $contests->filter(function ($c) use ($age) {
            $r = $c->rules;
            $ageGroup = $r?->age_group ?? 'all';
            $onlyAdults = (bool)($r?->only_adults ?? false) || $ageGroup === 'adults';

            if ($onlyAdults) {
                return !is_null($age) && $age >= 18;
            }

            if ($ageGroup === 'kids') {
                return !is_null($age) && $age < 13;
            }

            return true;
        })->values();


        $contests = $contests->filter(function ($c) {
            $required = (int)($c->rules?->trivia_questions_count ?? 1);
            $count = (int)($c->questions_count ?? 0);
            return $count >= $required;
        })->values();

return view('user.contests', compact('contests', 'needsBirthdateForAge'));

    }

    public function answerQuestion(Request $request, $id)
    {
        $request->validate([
            'answer' => ['required','string','max:255'],
        ]);

        return back()->with('success', 'Respuesta enviada (demo).');
    }
}
