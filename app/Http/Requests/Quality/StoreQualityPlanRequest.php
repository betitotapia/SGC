<?php

namespace App\Http\Requests\Quality;

use Illuminate\Foundation\Http\FormRequest;

class StoreQualityPlanRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('quality.plans.create'); }

    public function rules(): array
    {
        return [
            'folio'           => ['required','string','max:100','unique:quality_plans,folio'],
            'process'         => ['nullable','string','max:255'],
            'finding_type'    => ['nullable','string','max:255'],
            'finding'         => ['required','string'],
            'activity'        => ['nullable','string'],
            'root_cause'      => ['nullable','string'],
            'department'      => ['nullable','string','max:255'],
            'owner_name'      => ['nullable','string','max:255'],
            'owner_id'        => ['nullable','integer','exists:users,id'],
            'commitment_date' => ['nullable','date'],
            'close_date'      => ['nullable','date'],
            'status'          => ['required','string','max:100'], // manual
            'notes'           => ['nullable','string'],
        ];
    }
}
