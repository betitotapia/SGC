<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','permission:users.manage']);
    }

    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $users = User::query()
            ->when($q, fn($qry) => $qry->where('name','like',"%$q%")->orWhere('email','like',"%$q%"))
            ->with('department')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users','q'));
    }

    public function create()
    {
        $user = new User(['is_active'=>true]);
        $departments = Department::where('is_active',true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('user','departments','roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8'],
            'department_id' => ['nullable','exists:departments,id'],
            'role' => ['required','string','exists:roles,name'],
            'is_active' => ['nullable','boolean'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'department_id' => $data['department_id'] ?? null,
            'is_active' => (bool)($request->input('is_active', true)),
        ]);

        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')->with('ok','Usuario creado');
    }

    public function edit(User $user)
    {
        $departments = Department::where('is_active',true)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact('user','departments','roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email,'.$user->id],
            'password' => ['nullable','string','min:8'],
            'department_id' => ['nullable','exists:departments,id'],
            'role' => ['required','string','exists:roles,name'],
            'is_active' => ['nullable','boolean'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->department_id = $data['department_id'] ?? null;
        $user->is_active = (bool)($request->input('is_active', false));

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')->with('ok','Usuario actualizado');
    }
}