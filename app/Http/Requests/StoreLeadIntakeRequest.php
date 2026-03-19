<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadIntakeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:3000'],
            'details' => ['nullable', 'string', 'max:3000'],
            'interest_area' => ['nullable', 'string', 'max:100'],
            'current_stage' => ['nullable', 'string', 'max:100'],
            'team_size' => ['nullable', 'string', 'max:50'],
            'consent_marketing' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'consent_marketing' => $this->boolean('consent_marketing'),
        ]);
    }
}
