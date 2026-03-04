<?php

namespace App\Http\Requests\Quality;

use App\Models\QualityTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKanbanStatusRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('quality.kanban.manage'); }

    public function rules(): array
    {
        return [
            'task_id' => ['required','integer','exists:quality_tasks,id'],
            'status' => ['required', Rule::in([
                QualityTask::STATUS_OPEN,
                QualityTask::STATUS_IN_PROGRESS,
                QualityTask::STATUS_CLOSED,
            ])],
        ];
    }
}
