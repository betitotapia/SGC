<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quality\StoreQualityPlanRootAnalysisRequest;
use App\Http\Requests\Quality\UpdateQualityPlanRootAnalysisRequest;
use App\Models\QualityPlan;
use App\Models\QualityPlanRootAnalysis;
use App\Models\QualityPlanRootAnalysisFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class QualityPlanRootAnalysisController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:quality.plans.update']);
    }

    public function create(QualityPlan $plan): View
    {
        $analysis = new QualityPlanRootAnalysis();

        return view('quality.root_analyses.create', compact('plan', 'analysis'));
    }

    public function store(StoreQualityPlanRootAnalysisRequest $request, QualityPlan $plan): RedirectResponse
    {
        $validated = $request->validated();

        $team = [];
        $names = $request->input('team_names', []);
        $positions = $request->input('team_positions', []);

        foreach ($names as $i => $name) {
            $name = trim((string) $name);
            $position = trim((string) ($positions[$i] ?? ''));

            if ($name !== '' || $position !== '') {
                $team[] = [
                    'name' => $name,
                    'position' => $position,
                ];
            }
        }

        $analysis = $plan->rootAnalyses()->create([
            'analysis_description' => $validated['analysis_description'] ?? null,
            'analysis_team' => $team,
            'comments' => $validated['comments'] ?? null,
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if (!$file) {
                    continue;
                }

                $path = $file->store('root-analyses', 'public');

                $analysis->files()->create([
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size_bytes' => $file->getSize(),
                    'uploaded_by' => $request->user()->id,
                ]);
            }
        }

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Análisis de causa raíz agregado');
    }

    public function edit(QualityPlan $plan, QualityPlanRootAnalysis $rootAnalysis): View
    {
        abort_unless($rootAnalysis->plan_id === $plan->id, 404);

        $rootAnalysis->load('files');

        return view('quality.root_analyses.edit', [
            'plan' => $plan,
            'analysis' => $rootAnalysis,
        ]);
    }

    public function update(UpdateQualityPlanRootAnalysisRequest $request, QualityPlan $plan, QualityPlanRootAnalysis $rootAnalysis): RedirectResponse
{
    abort_unless($rootAnalysis->plan_id === $plan->id, 404);

    $validated = $request->validated();

    $team = [];
    $names = $request->input('team_names', []);
    $positions = $request->input('team_positions', []);

    foreach ($names as $i => $name) {
        $name = trim((string) $name);
        $position = trim((string) ($positions[$i] ?? ''));

        if ($name !== '' || $position !== '') {
            $team[] = [
                'name' => $name,
                'position' => $position,
            ];
        }
    }

    $rootAnalysis->update([
        'analysis_description' => $validated['analysis_description'] ?? null,
        'analysis_team' => $team,
        'comments' => $validated['comments'] ?? null,
    ]);

    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $file) {
            if (!$file) {
                continue;
            }

            $path = $file->store('root-analyses', 'public');

            $rootAnalysis->files()->create([
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
                'uploaded_by' => $request->user()->id,
            ]);
        }
    }

    return redirect()
        ->route('quality.plans.show', $plan)
        ->with('ok', 'Análisis de causa raíz actualizado');
}

    public function destroy(QualityPlan $plan, QualityPlanRootAnalysis $rootAnalysis): RedirectResponse
    {
        abort_unless($rootAnalysis->plan_id === $plan->id, 404);

        foreach ($rootAnalysis->files as $file) {
            if ($file->path && Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
        }

        $rootAnalysis->delete();

        return redirect()
            ->route('quality.plans.show', $plan)
            ->with('ok', 'Análisis de causa raíz eliminado');
    }

    public function destroyFile(QualityPlan $plan, QualityPlanRootAnalysis $rootAnalysis, QualityPlanRootAnalysisFile $file): RedirectResponse
    {
        abort_unless($rootAnalysis->plan_id === $plan->id, 404);
        abort_unless($file->root_analysis_id === $rootAnalysis->id, 404);

        if ($file->path && Storage::disk('public')->exists($file->path)) {
            Storage::disk('public')->delete($file->path);
        }

        $file->delete();

        return redirect()
            ->route('quality.root-analyses.edit', [$plan, $rootAnalysis])
            ->with('ok', 'Archivo eliminado');
    }
}