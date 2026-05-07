<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Quality\QualityPlanController;
use App\Http\Controllers\Quality\QualityTaskController;
use App\Http\Controllers\Quality\QualityTaskEvidenceController;
use App\Http\Controllers\Quality\QualityKanbanController;
use App\Http\Controllers\Quality\DepartmentController;
use App\Http\Controllers\Quality\QualityPlanMonitoringController;
use App\Http\Controllers\Quality\DocumentController;
use App\Http\Controllers\Quality\DocumentVersionController;
use App\Http\Controllers\Quality\DocumentApprovalController;
use App\Http\Controllers\Quality\DocumentApprovalTemplateController;
use App\Http\Controllers\Quality\DocumentMobileSignController;

// ── Firma móvil (sin autenticación — el token es la credencial) ───────────────
Route::prefix('quality')
    ->name('quality.')
    ->group(function () {
        Route::get('mobile-sign/{token}', [DocumentMobileSignController::class, 'show'])
            ->name('mobile-sign.show');
        Route::post('mobile-sign/{token}', [DocumentMobileSignController::class, 'store'])
            ->name('mobile-sign.store');
    });

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

        // ── Control Documental ────────────────────────────────────────────────
        Route::resource('documents', DocumentController::class);

        Route::get('documents/{document}/poll-status', [DocumentController::class, 'pollStatus'])
            ->name('documents.poll-status');

        // Firmas pendientes del usuario autenticado
        Route::get('pending-signatures', [DocumentApprovalController::class, 'pending'])
            ->name('documents.pending');

        // Imagen de firma autógrafa (disco privado)
        Route::get('approvals/{approval}/signature-image', [DocumentApprovalController::class, 'signatureImage'])
            ->name('documents.approvals.signature');

        Route::resource('approval-templates', DocumentApprovalTemplateController::class)
            ->middleware('permission:documents.manage_approvals')
            ->except(['show']);

        Route::prefix('documents/{document}')->group(function () {
            // Versiones
            Route::post('versions', [DocumentVersionController::class, 'store'])
                ->name('documents.versions.store');
            Route::delete('versions/{version}', [DocumentVersionController::class, 'destroy'])
                ->name('documents.versions.destroy');
            Route::get('versions/{version}/download', [DocumentVersionController::class, 'download'])
                ->name('documents.versions.download');
            Route::get('versions/{version}/certificate', [DocumentVersionController::class, 'downloadCertificate'])
                ->name('documents.versions.certificate');

            // Flujo de aprobación
            Route::post('submit', [DocumentApprovalController::class, 'submit'])
                ->name('documents.submit');
            Route::get('approvals/{approval}/sign', [DocumentApprovalController::class, 'showSign'])
                ->name('documents.approvals.show-sign');
            Route::post('approvals/{approval}/sign', [DocumentApprovalController::class, 'sign'])
                ->name('documents.approvals.sign');
            Route::get('approvals/{approval}/check-pending', [DocumentApprovalController::class, 'checkPending'])
                ->name('documents.approvals.check-pending');
            Route::post('approvals/{approval}/mobile-token', [DocumentMobileSignController::class, 'generateToken'])
                ->name('documents.approvals.mobile-token');
        });
    });
