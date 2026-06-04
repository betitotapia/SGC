<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quality\StoreTaskEvidenceRequest;
use App\Models\QualityTask;
use App\Models\QualityTaskEvidence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class QualityTaskEvidenceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:quality.plans.view'])->only('download');
        $this->middleware(['auth', 'permission:quality.evidences.create'])->only('store');
        $this->middleware(['auth', 'permission:quality.evidences.delete'])->only('destroy');
    }

    public function store(StoreTaskEvidenceRequest $request, QualityTask $task): RedirectResponse
    {
        $file = $request->file('file');
        $path = $file->store('task-evidences', 'public_ftp');

        $task->evidences()->create([
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);

        return back()->with('ok', 'Evidencia subida');
    }

    public function download(QualityTaskEvidence $evidence): Response|RedirectResponse
    {
        $this->authorizeAccess($evidence);

        if (! $evidence->path || ! Storage::disk('public_ftp')->exists($evidence->path)) {
            return back()->with('error', 'El archivo de evidencia no fue encontrado en el servidor.');
        }

        $filename = str_replace(['\\', '"'], ['', ''], $evidence->original_name ?: 'evidencia');

        return response(
            Storage::disk('public_ftp')->get($evidence->path),
            200,
            [
                'Content-Type' => $evidence->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]
        );
    }

    public function destroy(QualityTaskEvidence $evidence): RedirectResponse
    {
        if ($evidence->path && Storage::disk('public_ftp')->exists($evidence->path)) {
            Storage::disk('public_ftp')->delete($evidence->path);
        }

        $evidence->delete();

        return back()->with('ok', 'Evidencia eliminada');
    }

    protected function authorizeAccess(QualityTaskEvidence $evidence): void
    {
        $evidence->loadMissing('task.plan');

        $user = request()->user();
        $plan = $evidence->task?->plan;

        if (! $plan) {
            abort(404);
        }

        if (! $user->can('quality.plans.view_all') && $plan->department_id !== $user->department_id) {
            abort(403);
        }
    }
}
