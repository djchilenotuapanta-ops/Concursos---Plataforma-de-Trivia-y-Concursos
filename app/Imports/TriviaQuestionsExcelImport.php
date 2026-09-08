<?php

namespace App\Imports;

use App\Models\Contest;
use App\Models\ContestQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TriviaQuestionsExcelImport implements ToCollection, WithHeadingRow
{
    public function __construct(private Contest $contest)
    {
    }

    public int $imported = 0;
    public int $skipped = 0;

    public function collection(Collection $rows)
    {
        $maxOrder = (int) ContestQuestion::where('contest_id', $this->contest->id)->max('order');
        $nextOrder = $maxOrder + 1;

        foreach ($rows as $row) {
            $row = collect($row)->mapWithKeys(function ($v, $k) {
                $k = Str::of((string) $k)->lower()->trim()->replace([' ', '-', '.'], '_')->toString();
                return [$k => $v];
            });

            $payload = [
                'question' => $row->get('question'),
                'option_a' => $row->get('option_a'),
                'option_b' => $row->get('option_b'),
                'option_c' => $row->get('option_c'),
                'option_d' => $row->get('option_d'),
                'correct_option' => $row->get('correct_option'),
                'audience' => $row->get('audience'),
            ];

            if ($this->importPayload($payload, $nextOrder)) {
                $this->imported++;
                $nextOrder++;
            } else {
                $this->skipped++;
            }
        }
    }

    private function importPayload(array $payload, int $order): bool
    {
        $q = trim((string) ($payload['question'] ?? ''));
        $a = trim((string) ($payload['option_a'] ?? ''));
        $b = trim((string) ($payload['option_b'] ?? ''));
        $c = trim((string) ($payload['option_c'] ?? ''));
        $d = trim((string) ($payload['option_d'] ?? ''));

        $correct = strtoupper(trim((string) ($payload['correct_option'] ?? '')));
        if (in_array($correct, ['A', 'B', 'C', 'D'], true) === false) {
            $correct = Str::of($correct)->after('OPTION_')->upper()->toString();
        }

        $aud = strtolower(trim((string) ($payload['audience'] ?? '')));
        $audience = match ($aud) {
            'kid', 'kids', 'niño', 'niños' => 'kid',
            'adult', 'adulto', 'adultos' => 'adult',
            default => 'all',
        };

        if ($q === '' || $a === '' || $b === '') {
            return false;
        }

        if (!in_array($correct, ['A', 'B', 'C', 'D'], true)) {
            return false;
        }

        ContestQuestion::create([
            'contest_id' => $this->contest->id,
            'question' => $q,
            'option_a' => $a,
            'option_b' => $b,
            'option_c' => $c !== '' ? $c : null,
            'option_d' => $d !== '' ? $d : null,
            'correct_option' => $correct,
            'audience' => $audience,
            'order' => $order,
        ]);

        return true;
    }
}
