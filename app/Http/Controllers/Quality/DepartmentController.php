<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quality\StoreDepartmentRequest;
use App\Http\Requests\Quality\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct()
    {
        // Si tu Controller base ya está bien, esto funciona
        $this->middleware(['auth','permission:quality.departments.manage']);
    }

    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();

        $departments = Department::query()
            ->when($q, fn($query) =>
                $query->where('name', 'like', "%{$q}%")
            )
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('quality.departments.index', compact('departments','q'));
    }

    public function create(): View
    {
        $department = new Department(['is_active' => true]);
        return view('quality.departments.create', compact('department'));
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($request->input('is_active', true));

        Department::create($data);

        return redirect()->route('quality.departments.index')->with('ok', 'Departamento creado');
    }

    public function edit(Department $department): View
    {
        return view('quality.departments.edit', compact('department'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($request->input('is_active', false));

        $department->update($data);

        return redirect()->route('quality.departments.index')->with('ok', 'Departamento actualizado');
    }

    public function destroy(Department $department): RedirectResponse
    {
        // mejor “desactivar” que borrar, pero te dejo el delete por si lo quieres.
        // Recomendado: cambiar a is_active = false
        $department->delete();

        return redirect()->route('quality.departments.index')->with('ok', 'Departamento eliminado');
    }

    public function toggle(Department $department): RedirectResponse
    {
        $department->is_active = ! $department->is_active;
        $department->save();

        return back()->with('ok', 'Estatus actualizado');
    }
}