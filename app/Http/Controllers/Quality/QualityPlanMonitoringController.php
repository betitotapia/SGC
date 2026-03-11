<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quality\StoreQualityPlanMonitoringRequest;
use App\Http\Requests\Quality\UpdateQualityPlanMonitoringRequest;
use App\Models\QualityPlan;
use App\Models\QualityPlanMonitoring;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QualityPlanMonitoringController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:quality.plans.update']);
    }

    public function create(QualityPlan $plan): View
    {
        $monitoring = new QualityPlanMonitoring();

        return view('quality.monitorings.create', compact('plan', 'monitoring'));
    }

    public function store(StoreQualityPlanMonitoringRequest $request, QualityPlan $plan): RedirectResponse
    {
        $plan->monitorings()->create($request->validated());

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Monitoreo agregado');
    }

    public function edit(QualityPlan $plan, QualityPlanMonitoring $monitoring): View
    {
        abort_unless($monitoring->plan_id === $plan->id, 404);

        return view('quality.monitorings.edit', compact('plan', 'monitoring'));
    }

    public function update(UpdateQualityPlanMonitoringRequest $request, QualityPlan $plan, QualityPlanMonitoring $monitoring): RedirectResponse
    {
        abort_unless($monitoring->plan_id === $plan->id, 404);

        $monitoring->update($request->validated());

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Monitoreo actualizado');
    }

    public function destroy(QualityPlan $plan, QualityPlanMonitoring $monitoring): RedirectResponse
    {
        abort_unless($monitoring->plan_id === $plan->id, 404);

        $monitoring->delete();

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Monitoreo eliminado');
    }
}
