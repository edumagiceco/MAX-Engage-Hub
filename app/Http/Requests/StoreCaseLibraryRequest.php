<?php

namespace App\Http\Requests;

use App\Models\RecommendationResult;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCaseLibraryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'industry' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'problem' => ['required', 'string', 'max:180'],
            'solution_type' => ['required', Rule::in(RecommendationResult::TYPE_OPTIONS)],
            'summary' => ['required', 'string', 'max:3000'],
            'outcome' => ['nullable', 'string', 'max:3000'],
            'metrics_input' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['approved', 'draft'])],
        ];
    }
}
