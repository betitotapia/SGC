<?php

namespace App\Http\Requests\Quality;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQualityPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('quality.plans.update');
    }

    public function rules(): array
    {
        $planId = $this->route('plan')?->id ?? $this->route('plan');

        return [
            'folio'           => ['required', 'string', 'max:100', 'unique:quality_plans,folio,' . $planId],
            'open_date'       => ['nullable', 'date'],
            'origin'          => ['required', 'string', 'max:100'],
            'process'         => ['nullable', 'string', 'max:255'],
            'finding_type'    => ['required', 'string', 'max:100'],
            'detected_by'     => ['nullable', 'string', 'max:255'],
            'auditor_type'    => ['nullable', 'in:INTERNO,EXTERNO'],
            'finding'         => ['required', 'string'],
            'activity'        => ['nullable', 'string'],
            'root_cause'      => ['nullable', 'string'],
            'department_id'   => ['nullable', 'integer', 'exists:departments,id'],
            'owner_name'      => ['nullable', 'string', 'max:255'],
            'owner_email' => ['nullable', 'email', 'max:255'],
            'owner_id'        => ['nullable', 'integer', 'exists:users,id'],
            'commitment_date' => ['nullable', 'date'],
            'close_date'      => ['nullable', 'date'],
            'status'          => ['required', 'string', 'max:100'],
            'notes'           => ['nullable', 'string'],
        ];
    }
}