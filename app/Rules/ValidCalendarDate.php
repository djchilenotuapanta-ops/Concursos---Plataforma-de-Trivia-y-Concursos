<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCalendarDate implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $fail('⚠️ Las fechas ingresadas no son válidas. Revise el calendario.');
            return;
        }

        [$y, $m, $d] = array_map('intval', explode('-', $value));

        if (!checkdate($m, $d, $y)) {
            $fail('⚠️ La fecha ingresada no existe. Verifique el día y el mes.');
            return;
        }
    }
}
