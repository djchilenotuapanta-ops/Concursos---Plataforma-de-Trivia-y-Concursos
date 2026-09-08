<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EcuadorId implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $id = preg_replace('/\D+/', '', (string) $value);

        if ($id === '') {
            return;
        }

        $len = strlen($id);

        if ($len === 10) {
            (new EcuadorCedula())->validate($attribute, $id, $fail);
            return;
        }

        if ($len === 13) {
            $base = substr($id, 0, 10);
            $suffix = substr($id, 10, 3);

            $hasError = false;
            (new EcuadorCedula())->validate($attribute, $base, function ($msg) use (&$hasError) {
                $hasError = true;
            });

            if ($hasError) {
                $fail('El RUC no es válido (los primeros 10 dígitos deben corresponder a una cédula válida).');
                return;
            }

            if ($suffix === '000') {
                $fail('El RUC no es válido (el establecimiento no puede ser 000).');
                return;
            }

            return;
        }

        $fail('El número de identificación debe tener 10 dígitos (cédula) o 13 dígitos (RUC).');
    }
}
