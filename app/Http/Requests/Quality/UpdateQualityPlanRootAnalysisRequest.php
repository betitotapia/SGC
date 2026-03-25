<?php

namespace App\Http\Requests\Quality;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQualityPlanRootAnalysisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('quality.plans.update');
    }

    public function rules(): array
    {
        return [
            'analysis_description' => ['nullable', 'string'],
            'comments' => ['nullable', 'string'],

            'team_names' => ['nullable', 'array'],
            'team_names.*' => ['nullable', 'string', 'max:255'],

            'team_positions' => ['nullable', 'array'],
            'team_positions.*' => ['nullable', 'string', 'max:255'],

            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'file', 'max:10240'],
        ];
    }
}