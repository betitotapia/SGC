<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quality\StoreDocumentRequest;
use App\Http\Requests\Quality\UpdateDocumentRequest;
use App\Models\Department;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:documents.view'])->only(['index', 'show']);
        $this->middleware(['auth', 'permission:documents.create'])->only(['create', 'store']);
        $this->middleware(['auth', 'permission:documents.update'])->only(['edit', 'update']);
        $this->middleware(['auth', 'permission:documents.delete'])->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $q    = $request->string('q')->toString();
        $type = $request->string('type')->toString();

        $documents = Document::query()
            ->with(['department:id,name', 'creator:id,name', 'currentVersion'])
            ->when(! $user->can('documents.view_all'), fn ($qb) =>
                $qb->where('department_id', $user->department_id)
            )
            ->when($q, fn ($qb) =>
                $qb->where(fn ($sub) =>
                    $sub->where('folio', 'like', "%{$q}%")
                        ->orWhere('title', 'like', "%{$q}%")
                        ->orWhereHas('department', fn ($d) =>
                            $d->where('name', 'like', "%{$q}%")
                        )
                )
            )
            ->when($type, fn ($qb) => $qb->where('type', $type))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('quality.documents.index', compact('documents', 'q', 'type'));
    }

    public function create(): View
    {
        $document    = new Document();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $users       = User::where('is_active', true)->orderBy('name')->get();

        return view('quality.documents.create', compact('document', 'departments', 'users'));
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $department = Department::findOrFail($request->department_id);

        $document = Document::create([
            'folio'          => Document::generateFolio($request->type, $department),
            'title'          => $request->title,
            'type'           => $request->type,
            'department_id'  => $request->department_id,
            'status'         => Document::STATUS_DRAFT,
            'created_by'     => $request->user()->id,
            'elaboro_id'     => $request->elaboro_id    ?: null,
            'elaboro_cargo'  => $request->elaboro_cargo  ?: null,
            'reviso_id'      => $request->reviso_id     ?: null,
            'reviso_cargo'   => $request->reviso_cargo   ?: null,
            'autorizo_id'    => $request->autorizo_id   ?: null,
            'autorizo_cargo' => $request->autorizo_cargo ?: null,
        ]);

        \App\Support\Audit::log('document_created', $document, null, $document->toArray());

        return redirect()
            ->route('quality.documents.show', $document)
            ->with('ok', "Documento {$document->folio} creado correctamente.");
    }

    public function show(Document $document): View
    {
        $this->authorizeAccess($document);

        $document->load([
            'department',
            'creator',
            'elaboro',
            'reviso',
            'autorizo',
            'currentVersion.approvals.user',
            'versions.approvals.user',
            'versions.submitter',
        ]);

        return view('quality.documents.show', compact('document'));
    }

    public function edit(Document $document): View
    {
        $this->authorizeAccess($document);

        if (! $document->isEditable()) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'Solo se pueden editar documentos en estado Borrador.');
        }

        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $users       = User::where('is_active', true)->orderBy('name')->get();

        return view('quality.documents.edit', compact('document', 'departments', 'users'));
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $this->authorizeAccess($document);

        if (! $document->isEditable()) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'Solo se pueden editar documentos en estado Borrador.');
        }

        $before = $document->toArray();

        $document->update([
            'title'          => $request->title,
            'type'           => $request->type,
            'department_id'  => $request->department_id,
            'elaboro_id'     => $request->elaboro_id    ?: null,
            'elaboro_cargo'  => $request->elaboro_cargo  ?: null,
            'reviso_id'      => $request->reviso_id     ?: null,
            'reviso_cargo'   => $request->reviso_cargo   ?: null,
            'autorizo_id'    => $request->autorizo_id   ?: null,
            'autorizo_cargo' => $request->autorizo_cargo ?: null,
        ]);

        \App\Support\Audit::log('document_updated', $document, $before, $document->fresh()->toArray());

        return redirect()
            ->route('quality.documents.show', $document)
            ->with('ok', 'Documento actualizado.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorizeAccess($document);

        if (! $document->isEditable()) {
            return redirect()
                ->route('quality.documents.show', $document)
                ->with('error', 'Solo se pueden eliminar documentos en estado Borrador.');
        }

        \App\Support\Audit::log('document_deleted', $document, $document->toArray(), null);

        $document->delete();

        return redirect()
            ->route('quality.documents.index')
            ->with('ok', 'Documento eliminado.');
    }

    /**
     * Endpoint ligero para polling de estatus desde la vista show.
     */
    public function pollStatus(Document $document): \Illuminate\Http\JsonResponse
    {
        $this->authorizeAccess($document);

        return response()->json(['status' => $document->status]);
    }

    protected function authorizeAccess(Document $document): void
    {
        $user = request()->user();

        if (! $user->can('documents.view_all') && $document->department_id !== $user->department_id) {
            abort(403);
        }
    }
}
