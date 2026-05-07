<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DocumentApprovalTemplate;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentApprovalTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:documents.manage_approvals']);
    }

    public function index(Request $request): View
    {
        $departmentId = $request->integer('department_id') ?: null;

        $templates = DocumentApprovalTemplate::with(['department', 'user'])
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->orderBy('department_id')
            ->orderBy('document_type')
            ->orderBy('order')
            ->get()
            ->groupBy('department_id');

        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('quality.approval-templates.index', compact('templates', 'departments', 'departmentId'));
    }

    public function create(): View
    {
        $template    = new DocumentApprovalTemplate();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $users       = User::where('is_active', true)->orderBy('name')->get();

        return view('quality.approval-templates.create', compact('template', 'departments', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'department_id'    => ['required', 'integer', 'exists:departments,id'],
            'document_type'    => ['nullable', 'in:procedure,process,format,work_instruction'],
            'user_id'          => ['required', 'integer', 'exists:users,id'],
            'role_in_approval' => ['required', 'in:author,reviewer,approver'],
            'order'            => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        DocumentApprovalTemplate::create($data);

        return redirect()
            ->route('quality.approval-templates.index', ['department_id' => $data['department_id']])
            ->with('ok', 'Firmante agregado correctamente.');
    }

    public function edit(DocumentApprovalTemplate $approvalTemplate): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $users       = User::where('is_active', true)->orderBy('name')->get();

        return view('quality.approval-templates.edit', compact('approvalTemplate', 'departments', 'users'));
    }

    public function update(Request $request, DocumentApprovalTemplate $approvalTemplate): RedirectResponse
    {
        $data = $request->validate([
            'department_id'    => ['required', 'integer', 'exists:departments,id'],
            'document_type'    => ['nullable', 'in:procedure,process,format,work_instruction'],
            'user_id'          => ['required', 'integer', 'exists:users,id'],
            'role_in_approval' => ['required', 'in:author,reviewer,approver'],
            'order'            => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $approvalTemplate->update($data);

        return redirect()
            ->route('quality.approval-templates.index', ['department_id' => $approvalTemplate->department_id])
            ->with('ok', 'Firmante actualizado correctamente.');
    }

    public function destroy(DocumentApprovalTemplate $approvalTemplate): RedirectResponse
    {
        $deptId = $approvalTemplate->department_id;
        $approvalTemplate->delete();

        return redirect()
            ->route('quality.approval-templates.index', ['department_id' => $deptId])
            ->with('ok', 'Firmante eliminado.');
    }
}
