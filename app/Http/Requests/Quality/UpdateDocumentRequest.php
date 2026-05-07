<?php

namespace App\Http\Requests\Quality;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('documents.update');
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'type'           => ['required', 'in:procedure,process,format,work_instruction'],
            'department_id'  => ['required', 'integer', 'exists:departments,id'],
            'elaboro_cargo'  => ['nullable', 'string', 'max:150'],
            'reviso_cargo'   => ['nullable', 'string', 'max:150'],
            'autorizo_cargo' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title'         => 'título',
            'type'          => 'tipo',
            'department_id' => 'departamento',
        ];
    }
}
