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

        $task = new QualityTask(['status' => QualityTask::STATUS_OPEN]);
        $users = User::orderBy('name')->get();

        return view('quality.tasks.create', compact('plan', 'task', 'users'));
    }

    public function store(StoreQualityTaskRequest $request, QualityPlan $plan): RedirectResponse
    {
        $this->authorize('create', QualityTask::class);

        $data = $request->validated();
        $data['closed_at'] = ($data['status'] ?? null) === QualityTask::STATUS_CLOSED ? now() : null;

        $plan->tasks()->create($data);

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

        $data = $request->validated();

        if (($data['status'] ?? null) === QualityTask::STATUS_CLOSED) {
            $data['closed_at'] = $task->closed_at ?? now();
        } else {
            $data['closed_at'] = null;
        }

        $task->update($data);

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
        } else {
            $task->markClosed();
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

        // si estaba cerrada, la reabre
        if ($task->status === QualityTask::STATUS_CLOSED) {
            $task->status = QualityTask::STATUS_OPEN;
            $task->closed_at = null;
        }

        $task->save();

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Comentario de revisión guardado');
    }
}