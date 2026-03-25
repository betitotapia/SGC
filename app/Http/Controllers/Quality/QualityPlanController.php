<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quality\StoreQualityPlanRequest;
use App\Http\Requests\Quality\UpdateQualityPlanRequest;
use App\Models\Department;
use App\Models\QualityPlan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class QualityPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:quality.plans.view'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:quality.plans.create'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:quality.plans.update'])->only(['edit', 'update', 'saveFinalResult']);
        $this->middleware(['auth', 'permission:quality.plans.delete'])->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $q = $request->string('q')->toString();

        $plans = QualityPlan::query()
            ->with([
                'department:id,name',
                'owner:id,name,email',
            ])
            ->when(!$user->can('quality.plans.view_all'), function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('folio', 'like', "%{$q}%")
                        ->orWhere('finding', 'like', "%{$q}%")
                        ->orWhere('owner_name', 'like', "%{$q}%")
                        ->orWhere('status', 'like', "%{$q}%")
                        ->orWhereHas('department', function ($dq) use ($q) {
                            $dq->where('name', 'like', "%{$q}%");
                        });
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('quality.plans.index', compact('plans', 'q'));
    }

    public function create(): View
    {
        $plan = new QualityPlan(['status' => 'ABIERTO']);
        $users = User::orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('quality.plans.create', compact('plan', 'users', 'departments'));
    }

    public function store(StoreQualityPlanRequest $request): RedirectResponse
    {
        $plan = QualityPlan::create($request->validated() + [
            'progress' => 0,
        ]);

        \App\Support\QualityNotifier::planCreated($plan);

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Plan creado');
    }

    public function show(QualityPlan $plan): View
    {
        $user = request()->user();

        if (!$user->can('quality.plans.view_all') && $plan->department_id !== $user->department_id) {
            abort(403);
        }

        $plan->recalcProgress();
        $plan = $plan->fresh([
            'department',
            'owner',
            'tasks.evidences',
            'tasks.assignee',
            'tasks.reviewer',
            'monitorings',
            'rootAnalyses.files',
        ]);

        return view('quality.plans.show', compact('plan'));
    }

    public function edit(QualityPlan $plan): View
    {
        $user = request()->user();

        if (!$user->can('quality.plans.view_all') && $plan->department_id !== $user->department_id) {
            abort(403);
        }

        $users = User::orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('quality.plans.edit', compact('plan', 'users', 'departments'));
    }

    public function update(UpdateQualityPlanRequest $request, QualityPlan $plan): RedirectResponse
    {
        $oldStatus = $plan->status;

        $plan->update($request->validated());
        $plan->recalcProgress();

        if ($oldStatus !== 'CERRADO' && $plan->status === 'CERRADO') {
            \App\Support\QualityNotifier::planClosed($plan);
        }

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Plan actualizado');
    }

    public function destroy(QualityPlan $plan): RedirectResponse
    {
        \App\Support\Audit::deleted($plan, $plan->toArray());
        $plan->delete();

        return redirect()
            ->route('quality.plans.index')
            ->with('ok', 'Plan eliminado');
    }

    public function saveFinalResult(Request $request, QualityPlan $plan): RedirectResponse
    {
        $user = $request->user();
        $this->authorizePlanAccess($user, $plan);

        if ($plan->final_result) {
            return redirect()
                ->route('quality.plans.show', $plan)
                ->with('error', 'El resultado final ya fue registrado. Usa editar.');
        }

        $data = $request->validate([
            'final_result' => ['required', 'string', 'max:5000'],
        ]);

        $plan->update([
            'final_result' => $data['final_result'],
            'final_result_by' => $user->id,
            'final_result_at' => now(),
        ]);

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Resultado final guardado correctamente.');
    }

    public function updateFinalResult(Request $request, QualityPlan $plan)
                {
                    $user = $request->user();

                    $canManageFinalResult = $user
                        && (
                            $user->can('quality.plans.update')
                            || (method_exists($user, 'hasRole') && (
                                $user->hasRole('Administrador SGC')
                                || $user->hasRole('Administrador')
                                || $user->hasRole('Gerente')
                                || $user->hasRole('Admin')
                            ))
                        );

                    if (!$canManageFinalResult) {
                        abort(403);
                    }

                    $data = $request->validate([
                        'final_result' => ['required', 'string', 'max:5000'],
                    ]);

                    $plan->update([
                        'final_result' => $data['final_result'],
                        'final_result_by' => $user->id,
                        'final_result_at' => now(),
                    ]);

                    return redirect()
                        ->route('quality.plans.show', $plan)
                        ->with('ok', 'Resultado final actualizado correctamente.');
                }

   public function destroyFinalResult(Request $request, QualityPlan $plan)
        {
            $user = $request->user();

            $canManageFinalResult = $user
                && (
                    $user->can('quality.plans.update')
                    || (method_exists($user, 'hasRole') && (
                        $user->hasRole('Administrador SGC')
                        || $user->hasRole('Administrador')
                        || $user->hasRole('Gerente')
                        || $user->hasRole('Admin')
                    ))
                );

            if (!$canManageFinalResult) {
                abort(403);
            }

            $plan->update([
                'final_result' => null,
                'final_result_by' => null,
                'final_result_at' => null,
            ]);

            return redirect()
                ->route('quality.plans.show', $plan)
                ->with('ok', 'Resultado final eliminado correctamente.');
        }

    public function pdf(QualityPlan $plan): Response
    {
        $user = request()->user();
        $this->authorizePlanAccess($user, $plan);

        $plan->recalcProgress();
        $plan = $plan->fresh([
            'department',
            'owner',
            'tasks.evidences',
            'tasks.assignee',
            'tasks.reviewer',
            'monitorings',
            'rootAnalyses.files',
        ]);

        $pdf = Pdf::loadView('quality.plans.pdf', compact('plan'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("plan-accion-{$plan->folio}.pdf");
    }

    protected function authorizePlanAccess($user, QualityPlan $plan): void
    {
        if (!$user->can('quality.plans.view_all') && $plan->department_id !== $user->department_id) {
            abort(403);
        }
    }

    protected function canManageFinalResult($user): bool
    {
        return method_exists($user, 'hasAnyRole')
            && $user->hasAnyRole(['Gerente', 'Administrador SGC']);
    }
}
