<?php

namespace App\Services;

use App\Models\Contest;
use App\Models\ContestQuestion;
use Maatwebsite\Excel\Facades\Excel;

class TriviaImportService
{
    public static function parseExcel($file): array
    {
        $rows = Excel::toArray([], $file)[0] ?? [];
        if (!$rows) return [];

        $header = array_map(fn($h) => strtolower(trim((string)$h)), $rows[0]);
        $data = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = [];
            foreach ($header as $k => $key) {
                $row[$key] = $rows[$i][$k] ?? null;
            }
            $data[] = $row;
        }

        return $data;
    }

    public static function parseCsv(string $path): array
    {
        $content = file_get_contents($path);
        if (!$content) return [];

        $lines = preg_split('/\r\n|\r|\n/', $content);
        if (count($lines) < 2) return [];

        $header = str_getcsv($lines[0]);
        $header = array_map(fn($h) => strtolower(trim((string)$h)), $header);

        $data = [];
        for ($i = 1; $i < count($lines); $i++) {
            if (trim($lines[$i]) === '') continue;
            $values = str_getcsv($lines[$i]);

            $row = [];
            foreach ($header as $k => $key) {
                $row[$key] = $values[$k] ?? null;
            }
            $data[] = $row;
        }

        return $data;
    }

    public static function isValid(array $p): bool
    {
        return trim((string)($p['pregunta'] ?? $p['question'] ?? '')) !== ''
            && trim((string)($p['opcion_a'] ?? $p['option_a'] ?? '')) !== ''
            && trim((string)($p['opcion_b'] ?? $p['option_b'] ?? '')) !== ''
            && in_array(strtoupper(trim((string)($p['correcta'] ?? $p['correct_option'] ?? $p['correct'] ?? ''))), ['A','B','C','D'], true);
    }

    public static function normalizeAudience(?string $value): string
    {
        $a = strtolower(trim((string)$value));
        return match ($a) {
            'niño', 'niños', 'kid', 'kids' => 'kid',
            'adulto', 'adultos', 'adult' => 'adult',
            default => 'all',
        };
    }

    public static function normalize(array $p): array
    {
        $correct = strtoupper(trim((string)($p['correcta'] ?? $p['correct_option'] ?? $p['correct'] ?? '')));

        return [
            'question' => trim((string)($p['pregunta'] ?? $p['question'] ?? '')),
            'option_a' => trim((string)($p['opcion_a'] ?? $p['option_a'] ?? '')),
            'option_b' => trim((string)($p['opcion_b'] ?? $p['option_b'] ?? '')),
            'option_c' => trim((string)($p['opcion_c'] ?? $p['option_c'] ?? '')),
            'option_d' => trim((string)($p['opcion_d'] ?? $p['option_d'] ?? '')),
            'correct_option' => $correct,
            'audience' => self::normalizeAudience($p['audiencia'] ?? $p['audience'] ?? null),
        ];
    }

    public static function importToContest(Contest $contest, array $data): array
    {
        $valid = [];
        $invalid = [];

        foreach ($data as $p) {
            if (self::isValid($p)) {
                $valid[] = self::normalize($p);
            } else {
                $invalid[] = $p;
            }
        }

        foreach ($valid as $q) {
            ContestQuestion::create([
                'contest_id' => $contest->id,
                'question' => $q['question'],
                'option_a' => $q['option_a'],
                'option_b' => $q['option_b'],
                'option_c' => $q['option_c'],
                'option_d' => $q['option_d'],
                'correct_option' => $q['correct_option'],
                'audience' => $q['audience'],
            ]);
        }

        return [
            'valid_count' => count($valid),
            'invalid_count' => count($invalid),
        ];
    }

    public static function csvLine(array $fields): string
    {
        return implode(',', array_map(fn($v) => '"' . str_replace('"','""',(string)$v) . '"', $fields)) . "\n";
    }
}
