<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quality\UpdateKanbanStatusRequest;
use App\Models\QualityPlan;
use App\Models\QualityTask;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class QualityKanbanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','permission:quality.kanban.manage']);
    }

    public function show(QualityPlan $plan): View
    {
        $plan->load(['tasks.assignee']);

        $columns = [
            QualityTask::STATUS_OPEN => $plan->tasks->where('status', QualityTask::STATUS_OPEN)->values(),
            QualityTask::STATUS_IN_PROGRESS => $plan->tasks->where('status', QualityTask::STATUS_IN_PROGRESS)->values(),
            QualityTask::STATUS_CLOSED => $plan->tasks->where('status', QualityTask::STATUS_CLOSED)->values(),
        ];

        return view('quality.kanban.show', compact('plan','columns'));
    }

    public function updateStatus(UpdateKanbanStatusRequest $request, QualityPlan $plan): JsonResponse
    {
        $task = QualityTask::where('plan_id', $plan->id)->findOrFail($request->integer('task_id'));
        $status = $request->string('status')->toString();

        $data = ['status' => $status];

        if ($status === QualityTask::STATUS_CLOSED) {
            $data['closed_at'] = $task->closed_at ?? now();
        } else {
            $data['closed_at'] = null;
        }

        $task->update($data);

        return response()->json([
            'ok' => true,
            'task_id' => $task->id,
            'status' => $task->status,
        ]);
    }
}
