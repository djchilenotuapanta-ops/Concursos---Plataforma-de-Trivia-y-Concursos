<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'company_name' => ['required','string','min:2','max:255'],
            'razon_social' => ['required','string','min:2','max:255'],
            'ruc' => ['required','string','min:10','max:13','unique:users,ruc,' . $userId],
            'phone' => ['nullable','string','min:7','max:20'],
            'address' => ['nullable','string','min:3','max:255'],
            'city' => ['nullable','string','min:2','max:100'],
            'representative_name' => ['nullable','string','min:3','max:255'],
            'website' => ['nullable','string','min:3','max:255'],
            'description' => ['nullable','string','min:3','max:1000'],
            'bank_name' => ['nullable','string','min:2','max:120'],
            'bank_account' => ['nullable','string','min:4','max:50'],
            'logo' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ];
    }
}
