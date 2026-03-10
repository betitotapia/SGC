<?php

namespace App\Http\Requests\Quality;

use Illuminate\Foundation\Http\FormRequest;

class ReviewQualityTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('quality.tasks.update');
    }

    public function rules(): array
    {
        return [
            'review_comment' => ['required','string','max:2000'],
        ];
    }
}
