<?php

namespace App\Http\Requests\Quality;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQualityPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('quality.plans.create');
    }

    public function rules(): array
    {
        $processValues = [
            'PRO-DIR-01 PROCESO DE DIRECCIÓN',
            'PRO-SO-01 PROCESO DE SOPORTE ORGANIZACIONAL',
            'PRO-RH-01 PROCESO DE RECURSOS HUMANOS',
            'PRO-AF-01 PROCESO DE ADMINISTRACIÓN Y FINANZAS',
            'PRO-COM-01 PROCESO DE COMPRAS',
            'PRO-VNT-01 PROCESO DE VENTAS',
            'PRO-AT-01 PROCESO DE ATENCIÓN A CLIENTES',
            'PRO-AL-01 PROCESO DE ALMACÉN Y LOGÍSTICA',
            'PRO-SMAM-01 PROCESO DE SEG., MED., ANÁLISIS Y MEJORA',
        ];

        return [
            'folio'           => ['required', 'string', 'max:100', 'unique:quality_plans,folio'],
            'open_date'       => ['nullable', 'date'],
            'origin'          => ['required', 'string', 'max:100'],
            'process'         => ['nullable', 'string', 'max:255', Rule::in($processValues)],
            'finding_type' => ['required', 'string', 'max:100'],
            'detected_by'     => ['nullable', 'string', 'max:255'],
            'auditor_type'    => ['nullable', 'in:INTERNO,EXTERNO'],
            'finding'         => ['required', 'string'],
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
