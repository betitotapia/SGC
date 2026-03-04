<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quality\StoreQualityPlanRequest;
use App\Http\Requests\Quality\UpdateQualityPlanRequest;
use App\Models\QualityPlan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Department;


class QualityPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','permission:quality.plans.view'])->only(['index','show']);
        $this->middleware(['auth','permission:quality.plans.create'])->only(['create','store']);
        $this->middleware(['auth','permission:quality.plans.update'])->only(['edit','update']);
        $this->middleware(['auth','permission:quality.plans.delete'])->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();

        $plans = QualityPlan::query()
            ->when($q, function ($query) use ($q) {
                $query->where('folio', 'like', "%{$q}%")
                    ->orWhere('finding', 'like', "%{$q}%")
                    ->orWhere('department', 'like', "%{$q}%")
                    ->orWhere('owner_name', 'like', "%{$q}%")
                    ->orWhere('status', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

            $user = $request->user();
            $plans = \App\Models\QualityPlan::query()->when(!$user->isQuality(), function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })
            ->latest()
            ->paginate(15);

        return view('quality.plans.index', compact('plans', 'q'));
    }

    public function create(): View
    {
        $plan = new QualityPlan(['status' => 'ABIERTO']);
        $users = User::orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('quality.plans.create', compact('plan','users','departments'));
    }

    public function store(StoreQualityPlanRequest $request): RedirectResponse
    {
        $plan = QualityPlan::create($request->validated() + ['progress' => 0]);
        return redirect()->route('quality.plans.show', $plan)->with('ok', 'Plan creado');
    }

    public function show(QualityPlan $plan): View
    {
        $user = request()->user();
        if (!$user->isQuality() && $plan->department_id !== $user->department_id) {
            abort(403);
        }
        $plan->load(['tasks.evidences','tasks.assignee','owner']);
        $plan->recalcProgress();
        $plan->refresh();
        return view('quality.plans.show', compact('plan'));
    }

    public function edit(QualityPlan $plan): View
    {
        $users = User::orderBy('name')->get();
       $departments = Department::where('is_active', true)->orderBy('name')->get();
       return view('quality.plans.edit', compact('plan','users','departments'));
    }

    public function update(UpdateQualityPlanRequest $request, QualityPlan $plan): RedirectResponse
    {
        $plan->update($request->validated());
        $plan->recalcProgress();
        return redirect()->route('quality.plans.show', $plan)->with('ok', 'Plan actualizado');
    }

    public function destroy(QualityPlan $plan): RedirectResponse
    {
       $this->middleware(['auth','permission:quality.plans.delete']); // ya lo tienes
        \App\Support\Audit::deleted($plan, $plan->toArray());
        $plan->delete();
        return redirect()->route('quality.plans.index')->with('ok', 'Plan eliminado');
    }
}
