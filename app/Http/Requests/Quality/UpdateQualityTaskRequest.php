<?php

namespace App\Http\Requests\Quality;

use App\Models\QualityTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQualityTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('quality.tasks.manage');
    }

    public function rules(): array
    {
        return [
            'title'           => ['required','string','max:255'],
            'description'     => ['nullable','string'],
            'comments'        => ['nullable','string'],
            'commitment_date' => ['nullable','date'],
            'assignee_id'     => ['nullable','integer','exists:users,id'],
            'status'          => ['required', Rule::in([
                QualityTask::STATUS_OPEN,
                QualityTask::STATUS_IN_PROGRESS,
                QualityTask::STATUS_CLOSED,
            ])],
        ];
    }
}