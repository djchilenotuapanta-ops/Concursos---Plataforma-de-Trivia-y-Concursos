<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'birthdate' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'cedula' => ['required', 'digits_between:10,13', 'unique:users,cedula,' . $userId],
            'phone' => ['nullable','string','max:25'],
        ];
    }

    public function messages(): array
    {
        return [
            'birthdate.required' => 'Debes ingresar tu fecha de nacimiento para que el sistema calcule tu edad.',
            'birthdate.before' => 'La fecha de nacimiento no puede ser hoy ni una fecha futura.',
            'cedula.unique' => 'Esta cédula ya está registrada por otro usuario.',
        ];
    }
}
