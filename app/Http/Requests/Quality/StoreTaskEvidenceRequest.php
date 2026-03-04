<?php

namespace App\Http\Requests\Quality;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskEvidenceRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('quality.evidences.manage'); }

    public function rules(): array
    {
        return [
            'file' => ['required','file','max:20480'],
        ];
    }
}
