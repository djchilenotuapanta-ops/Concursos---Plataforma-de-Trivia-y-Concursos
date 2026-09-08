<?php

namespace App\Rules;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ReasonableDate implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        if (!is_string($value) || !preg_match('/^(\d{4})-\d{2}-\d{2}$/', $value, $m)) {
            return;
        }

        $year = (int) $m[1];

        $maxYears = SystemSetting::getInt('max_years_in_future', config('ganafacil.max_years_in_future', 5));
        $currentYear = (int) now()->format('Y');

        if ($year < 2000) {
            $fail('⚠️ Las fechas ingresadas no son válidas. Revise el calendario.');
            return;
        }

        if ($year > ($currentYear + $maxYears)) {
            $fail('⚠️ Las fechas ingresadas no son válidas. Revise el calendario.');
            return;
        }
    }
}
