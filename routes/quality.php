<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Quality\QualityPlanController;
use App\Http\Controllers\Quality\QualityTaskController;
use App\Http\Controllers\Quality\QualityTaskEvidenceController;
use App\Http\Controllers\Quality\QualityKanbanController;
use App\Http\Controllers\Quality\DepartmentController;
use App\Http\Controllers\Quality\QualityPlanMonitoringController;

Route::middleware(['auth'])
    ->prefix('quality')
    ->name('quality.')
    ->group(function () {

        Route::resource('plans', QualityPlanController::class);

        Route::prefix('plans/{plan}')->group(function () {
            Route::resource('tasks', QualityTaskController::class)->except(['index', 'show']);
            Route::post('tasks/{task}/toggle', [QualityTaskController::class, 'toggle'])->name('tasks.toggle');
            Route::post('tasks/{task}/review', [QualityTaskController::class, 'review'])->name('tasks.review');
            Route::post('tasks/reorder', [\App\Http\Controllers\Quality\QualityTaskController::class, 'reorder'])
                ->name('tasks.reorder');

            Route::get('kanban', [QualityKanbanController::class, 'show'])->name('kanban.show');
            Route::post('kanban/status', [QualityKanbanController::class, 'updateStatus'])->name('kanban.status');

            Route::get('monitorings/create', [QualityPlanMonitoringController::class, 'create'])->name('monitorings.create');
            Route::post('monitorings', [QualityPlanMonitoringController::class, 'store'])->name('monitorings.store');
            Route::get('monitorings/{monitoring}/edit', [QualityPlanMonitoringController::class, 'edit'])->name('monitorings.edit');
            Route::put('monitorings/{monitoring}', [QualityPlanMonitoringController::class, 'update'])->name('monitorings.update');
            Route::delete('monitorings/{monitoring}', [QualityPlanMonitoringController::class, 'destroy'])->name('monitorings.destroy');

            Route::post('final-result', [QualityPlanController::class, 'saveFinalResult'])
                ->name('plans.final-result');

            Route::put('final-result', [QualityPlanController::class, 'updateFinalResult'])
                ->name('plans.final-result.update');

            Route::delete('final-result', [QualityPlanController::class, 'destroyFinalResult'])
                ->name('plans.final-result.destroy');

            Route::get('root-analyses/create', [\App\Http\Controllers\Quality\QualityPlanRootAnalysisController::class, 'create'])
                ->name('root-analyses.create');

            Route::post('root-analyses', [\App\Http\Controllers\Quality\QualityPlanRootAnalysisController::class, 'store'])
                ->name('root-analyses.store');

            Route::get('root-analyses/{rootAnalysis}/edit', [\App\Http\Controllers\Quality\QualityPlanRootAnalysisController::class, 'edit'])
                ->name('root-analyses.edit');

            Route::put('root-analyses/{rootAnalysis}', [\App\Http\Controllers\Quality\QualityPlanRootAnalysisController::class, 'update'])
                ->name('root-analyses.update');

            Route::delete('root-analyses/{rootAnalysis}', [\App\Http\Controllers\Quality\QualityPlanRootAnalysisController::class, 'destroy'])
                ->name('root-analyses.destroy');

            Route::delete('root-analyses/{rootAnalysis}/files/{file}', [\App\Http\Controllers\Quality\QualityPlanRootAnalysisController::class, 'destroyFile'])
                ->name('root-analyses.files.destroy');   

        });

        Route::post('tasks/{task}/evidences', [QualityTaskEvidenceController::class, 'store'])
            ->middleware(['permission:quality.evidences.create'])
            ->name('tasks.evidences.store');

        Route::delete('evidences/{evidence}', [QualityTaskEvidenceController::class, 'destroy'])
            ->middleware(['permission:quality.evidences.delete'])
            ->name('evidences.destroy');

        Route::middleware(['permission:quality.departments.manage'])->group(function () {
            Route::resource('departments', DepartmentController::class)->except(['show']);
            Route::post('departments/{department}/toggle', [DepartmentController::class, 'toggle'])->name('departments.toggle');
        });

        Route::get('plans/{plan}/pdf', [QualityPlanController::class, 'pdf'])->name('plans.pdf');
    });
