<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\QualityPlan;
use App\Models\QualityTask;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
{
    $user = auth()->user();
    $today = \Carbon\Carbon::today();
    $next7Days = \Carbon\Carbon::today()->addDays(7);

    $isQuality = method_exists($user, 'isQuality') ? $user->isQuality() : false;

    /*
    |--------------------------------------------------------------------------
    | Queries base
    |--------------------------------------------------------------------------
    */
    $plansQuery = \App\Models\QualityPlan::query()->with('department');

    $tasksQuery = \App\Models\QualityTask::query()
        ->with(['plan.department']);

    if (!$isQuality && !empty($user->department_id)) {
        $plansQuery->where('department_id', $user->department_id);

        $tasksQuery->whereHas('plan', function ($q) use ($user) {
            $q->where('department_id', $user->department_id);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | KPIs generales
    |--------------------------------------------------------------------------
    */
    $totalPlans = (clone $plansQuery)->count();

    $openPlans = (clone $plansQuery)
        ->where('quality_plans.status', '!=', 'CERRADO')
        ->count();

    $closedPlans = (clone $plansQuery)
        ->where('quality_plans.status', 'CERRADO')
        ->count();

    $overdueTasks = (clone $tasksQuery)
        ->whereIn('quality_tasks.status', [
            \App\Models\QualityTask::STATUS_OPEN,
            \App\Models\QualityTask::STATUS_IN_PROGRESS
        ])
        ->whereDate('quality_tasks.commitment_date', '<', $today)
        ->count();

    $upcomingTasks = (clone $tasksQuery)
        ->whereIn('quality_tasks.status', [
            \App\Models\QualityTask::STATUS_OPEN,
            \App\Models\QualityTask::STATUS_IN_PROGRESS
        ])
        ->whereBetween('quality_tasks.commitment_date', [$today, $next7Days])
        ->count();

    /*
    |--------------------------------------------------------------------------
    | Gráfica: planes abiertos por departamento
    |--------------------------------------------------------------------------
    */
    $plansByDepartment = \App\Models\Department::query()
        ->withCount([
            'plans as open_plans_count' => function ($q) use ($isQuality, $user) {
                $q->where('quality_plans.status', '!=', 'CERRADO');

                if (!$isQuality && !empty($user->department_id)) {
                    $q->where('quality_plans.department_id', $user->department_id);
                }
            }
        ])
        ->when(!$isQuality && !empty($user->department_id), function ($q) use ($user) {
            $q->where('departments.id', $user->department_id);
        })
        ->orderBy('name')
        ->get();

    $chartLabels = $plansByDepartment->pluck('name')->values();
    $chartValues = $plansByDepartment->pluck('open_plans_count')->values();

    /*
    |--------------------------------------------------------------------------
    | Tabla: áreas con tareas pendientes / por vencer / vencidas
    |--------------------------------------------------------------------------
    */
    $areasRisk = \App\Models\Department::query()
        ->withCount([
            'plans as open_plans_count' => function ($q) use ($isQuality, $user) {
                $q->where('quality_plans.status', '!=', 'CERRADO');

                if (!$isQuality && !empty($user->department_id)) {
                    $q->where('quality_plans.department_id', $user->department_id);
                }
            },

            'tasks as pending_tasks_count' => function ($q) use ($isQuality, $user) {
                $q->whereIn('quality_tasks.status', [
                    \App\Models\QualityTask::STATUS_OPEN,
                    \App\Models\QualityTask::STATUS_IN_PROGRESS
                ]);

                if (!$isQuality && !empty($user->department_id)) {
                    $q->whereHas('plan', function ($sub) use ($user) {
                        $sub->where('quality_plans.department_id', $user->department_id);
                    });
                }
            },

            'tasks as upcoming_tasks_count' => function ($q) use ($today, $next7Days, $isQuality, $user) {
                $q->whereIn('quality_tasks.status', [
                    \App\Models\QualityTask::STATUS_OPEN,
                    \App\Models\QualityTask::STATUS_IN_PROGRESS
                ])
                ->whereBetween('quality_tasks.commitment_date', [$today, $next7Days]);

                if (!$isQuality && !empty($user->department_id)) {
                    $q->whereHas('plan', function ($sub) use ($user) {
                        $sub->where('quality_plans.department_id', $user->department_id);
                    });
                }
            },

            'tasks as overdue_tasks_count' => function ($q) use ($today, $isQuality, $user) {
                $q->whereIn('quality_tasks.status', [
                    \App\Models\QualityTask::STATUS_OPEN,
                    \App\Models\QualityTask::STATUS_IN_PROGRESS
                ])
                ->whereDate('quality_tasks.commitment_date', '<', $today);

                if (!$isQuality && !empty($user->department_id)) {
                    $q->whereHas('plan', function ($sub) use ($user) {
                        $sub->where('quality_plans.department_id', $user->department_id);
                    });
                }
            },
        ])
        ->when(!$isQuality && !empty($user->department_id), function ($q) use ($user) {
            $q->where('departments.id', $user->department_id);
        })
        ->orderByDesc('overdue_tasks_count')
        ->orderByDesc('upcoming_tasks_count')
        ->get();

    return view('dashboard', compact(
        'totalPlans',
        'openPlans',
        'closedPlans',
        'overdueTasks',
        'upcomingTasks',
        'chartLabels',
        'chartValues',
        'areasRisk'
    ));
}
}
