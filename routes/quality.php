<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Quality\QualityPlanController;
use App\Http\Controllers\Quality\QualityTaskController;
use App\Http\Controllers\Quality\QualityTaskEvidenceController;
use App\Http\Controllers\Quality\QualityKanbanController;
use App\Http\Controllers\Quality\DepartmentController;

Route::middleware(['auth'])
    ->prefix('quality')
    ->name('quality.')
    ->group(function () {

        Route::resource('plans', QualityPlanController::class);

        Route::prefix('plans/{plan}')->group(function () {
            Route::resource('tasks', QualityTaskController::class)->except(['index', 'show']);
            Route::post('tasks/{task}/toggle', [QualityTaskController::class, 'toggle'])->name('tasks.toggle');
            Route::post('tasks/{task}/review', [QualityTaskController::class, 'review'])->name('tasks.review');

            Route::get('kanban', [QualityKanbanController::class, 'show'])->name('kanban.show');
            Route::post('kanban/status', [QualityKanbanController::class, 'updateStatus'])->name('kanban.status');

           
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