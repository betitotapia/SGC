<?php

namespace App\Http\Requests\Quality;

use Illuminate\Foundation\Http\FormRequest;

class StoreQualityPlanMonitoringRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('quality.plans.update');
    }

    public function rules(): array
    {
        return [
            'period' => ['nullable', 'string', 'max:100'],
            'activity_to_monitor' => ['required', 'string'],
            'responsible_name' => ['nullable', 'string', 'max:255'],
            'is_effective' => ['nullable', 'in:1,0'],
            'target_goal' => ['nullable', 'string'],
            'goal_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'action_close_date' => ['nullable', 'date'],
            'final_result' => ['nullable', 'string'],
        ];
    }
}

