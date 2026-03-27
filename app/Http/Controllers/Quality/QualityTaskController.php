<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quality\ReviewQualityTaskRequest;
use App\Http\Requests\Quality\StoreQualityTaskRequest;
use App\Http\Requests\Quality\UpdateQualityTaskRequest;
use App\Models\QualityPlan;
use App\Models\QualityTask;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QualityTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:quality.tasks.manage']);
    }

    public function create(QualityPlan $plan): View
    {
        $this->authorize('create', QualityTask::class);

        $task = new QualityTask([
            'status' => QualityTask::STATUS_OPEN
        ]);

        $users = User::orderBy('name')->get();

        return view('quality.tasks.create', compact('plan', 'task', 'users'));
    }

    public function store(StoreQualityTaskRequest $request, QualityPlan $plan): RedirectResponse
    {
        $this->authorize('create', QualityTask::class);

        $data = $request->validated();
        $data['closed_at'] = ($data['status'] ?? null) === QualityTask::STATUS_CLOSED ? now() : null;

        $nextOrder = $plan->tasks()->max('sort_order');
        $nextOrder = $nextOrder ? $nextOrder + 1 : 1;

        $data['sort_order'] = $nextOrder;
        $task = $plan->tasks()->create($data);

        \App\Support\QualityNotifier::taskCreated($plan, $task);

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Tarea creada');
    }

    public function edit(QualityPlan $plan, QualityTask $task): View
    {
        abort_unless($task->plan_id === $plan->id, 404);
        $this->authorize('update', $task);

        $users = User::orderBy('name')->get();

        return view('quality.tasks.edit', compact('plan', 'task', 'users'));
    }

    public function update(UpdateQualityTaskRequest $request, QualityPlan $plan, QualityTask $task): RedirectResponse
    {
        abort_unless($task->plan_id === $plan->id, 404);
        $this->authorize('update', $task);

        $oldStatus = $task->status;

        $data = $request->validated();

        if (($data['status'] ?? null) === QualityTask::STATUS_CLOSED) {
            $data['closed_at'] = $task->closed_at ?? now();
        } else {
            $data['closed_at'] = null;
        }

        $task->update($data);

        if ($oldStatus !== QualityTask::STATUS_CLOSED && $task->status === QualityTask::STATUS_CLOSED) {
            \App\Support\QualityNotifier::taskClosed($plan, $task);
        } else {
            \App\Support\QualityNotifier::taskUpdated($plan, $task);
        }

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Tarea actualizada');
    }

    public function destroy(QualityPlan $plan, QualityTask $task): RedirectResponse
    {
        abort_unless($task->plan_id === $plan->id, 404);
        $this->authorize('delete', $task);

        Audit::deleted($task, $task->toArray());
        $task->delete();
        $remainingTasks = $plan->tasks()->orderBy('sort_order')->orderBy('id')->get();
        foreach ($remainingTasks as $index => $item) {
            $item->update([
                'sort_order' => $index + 1,
            ]);
        }

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Tarea eliminada');
    }

    public function toggle(QualityPlan $plan, QualityTask $task): RedirectResponse
    {
        abort_unless($task->plan_id === $plan->id, 404);
        $this->authorize('update', $task);

        if ($task->status === QualityTask::STATUS_CLOSED) {
            $task->markOpen();
            \App\Support\QualityNotifier::taskReopened($plan, $task);
        } else {
            $task->markClosed();
            \App\Support\QualityNotifier::taskClosed($plan, $task);
        }

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Estatus de tarea actualizado');
    }

    public function review(ReviewQualityTaskRequest $request, QualityPlan $plan, QualityTask $task): RedirectResponse
    {
        abort_unless($task->plan_id === $plan->id, 404);
        $this->authorize('update', $task);

        $task->review_comment = $request->validated()['review_comment'];
        $task->reviewed_at = now();
        $task->reviewed_by = auth()->id();

        if ($task->status === QualityTask::STATUS_CLOSED) {
            $task->status = QualityTask::STATUS_OPEN;
            $task->closed_at = null;
        }

        $task->save();

        \App\Support\QualityNotifier::taskCommented($plan, $task);

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Comentario de revisión guardado');
    }

    public function reorder(\Illuminate\Http\Request $request, QualityPlan $plan): \Illuminate\Http\JsonResponse
        {
            $this->authorize('update', \App\Models\QualityTask::class);

            $request->validate([
                'tasks' => ['required', 'array'],
                'tasks.*' => ['integer'],
            ]);

            $taskIds = $request->input('tasks');

            $tasks = $plan->tasks()->whereIn('id', $taskIds)->get()->keyBy('id');

            foreach ($taskIds as $index => $taskId) {
                if (isset($tasks[$taskId])) {
                    $tasks[$taskId]->update([
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            return response()->json([
                'ok' => true,
            ]);
        }
}