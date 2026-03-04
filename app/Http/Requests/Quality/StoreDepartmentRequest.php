<?php

namespace App\Http\Requests\Quality;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('quality.departments.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:150','unique:departments,name'],
            'is_active' => ['nullable','boolean'],
        ];
    }
}