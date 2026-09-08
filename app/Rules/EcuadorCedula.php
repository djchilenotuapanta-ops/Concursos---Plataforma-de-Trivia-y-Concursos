<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EcuadorCedula implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cedula = preg_replace('/\D+/', '', (string) $value);

        if ($cedula === '') {
            return;
        }

        if (strlen($cedula) !== 10) {
            $fail('La cédula debe tener exactamente 10 dígitos.');
            return;
        }

        $provincia = (int) substr($cedula, 0, 2);
        if ($provincia < 1 || $provincia > 24) {
            $fail('La cédula no es válida (provincia incorrecta).');
            return;
        }

        $tercer = (int) $cedula[2];
        if ($tercer < 0 || $tercer > 5) {
            $fail('La cédula no es válida (tercer dígito incorrecto).');
            return;
        }

        $coef = [2,1,2,1,2,1,2,1,2];
        $suma = 0;

        for ($i = 0; $i < 9; $i++) {
            $n = (int) $cedula[$i] * $coef[$i];
            if ($n >= 10) $n -= 9;
            $suma += $n;
        }

        $verificador = (int) $cedula[9];
        $decena = (int) (ceil($suma / 10) * 10);
        $digito = $decena - $suma;
        if ($digito === 10) $digito = 0;

        if ($digito !== $verificador) {
            $fail('La cédula no es válida (dígito verificador incorrecto).');
        }
    }
}
