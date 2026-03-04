<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quality\StoreTaskEvidenceRequest;
use App\Models\QualityTask;
use App\Models\QualityTaskEvidence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class QualityTaskEvidenceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','permission:quality.evidences.manage']);
    }

    public function store(StoreTaskEvidenceRequest $request, QualityTask $task): RedirectResponse
    {
        $file = $request->file('file');
        $path = $file->store('task-evidences', 'public');

        $task->evidences()->create([
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
            'mime_type'     => $file->getMimeType(),
            'size_bytes'    => $file->getSize(),
            'uploaded_by'   => $request->user()->id,
        ]);

        return back()->with('ok', 'Evidencia subida');
    }

    public function destroy(QualityTaskEvidence $evidence): RedirectResponse
    {
        if ($evidence->path && Storage::disk('public')->exists($evidence->path)) {
            Storage::disk('public')->delete($evidence->path);
        }

        $evidence->delete();
        return back()->with('ok', 'Evidencia eliminada');
    }
}
