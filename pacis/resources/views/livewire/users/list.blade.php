<div class="space-y-6">
    <div class="flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Usuarios</h1>
            <p class="text-sm text-gray-500">Administradores, vendedores, facturación y almacén.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-primary">+ Nuevo usuario</a>
    </div>

    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="bg-white shadow-sm rounded-lg border border-gray-100">
        <div class="p-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o email..." class="input w-full md:w-[420px]">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Roles</th>
                        <th class="px-4 py-3 text-center">Activo</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $u)
                        <tr wire:key="u-{{ $u->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
                            <td class="px-4 py-3">{{ $u->email }}</td>
                            <td class="px-4 py-3">
                                @foreach ($u->roles as $r)
                                    <span class="badge badge-blue">{{ $r->name }}</span>
                                @endforeach
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggle({{ $u->id }})" class="text-xs {{ $u->active ? 'text-green-700' : 'text-gray-400' }}">
                                    {{ $u->active ? '● Activo' : '○ Inactivo' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('users.edit', $u) }}" class="text-pacis-600 hover:underline">Editar</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Sin resultados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $users->links() }}</div>
    </div>
</div>
