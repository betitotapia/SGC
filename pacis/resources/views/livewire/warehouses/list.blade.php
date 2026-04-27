<div class="space-y-6">
    <div class="flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Almacenes</h1>
            <p class="text-sm text-gray-500">Gestiona los centros de distribución y sucursales.</p>
        </div>
        @can('warehouses.create')
            <a href="{{ route('warehouses.create') }}" class="btn-primary">+ Nuevo almacén</a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow-sm rounded-lg border border-gray-100">
        <div class="p-4 flex gap-3 flex-wrap">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre, clave o ciudad..." class="input w-72">
            <select wire:model.live="status" class="input w-44">
                <option value="all">Todos</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Clave</th>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Ciudad</th>
                        <th class="px-4 py-3 text-left">Encargado</th>
                        <th class="px-4 py-3 text-center">Default</th>
                        <th class="px-4 py-3 text-center">Activo</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($warehouses as $w)
                        <tr wire:key="wh-{{ $w->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-pacis-700">{{ $w->code }}</td>
                            <td class="px-4 py-3 font-medium">{{ $w->name }}</td>
                            <td class="px-4 py-3">{{ $w->city }}</td>
                            <td class="px-4 py-3">{{ $w->manager }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($w->is_default)<span class="badge badge-blue">Default</span>@endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggle({{ $w->id }})" class="text-xs {{ $w->active ? 'text-green-700' : 'text-gray-400' }}">
                                    {{ $w->active ? '● Activo' : '○ Inactivo' }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @can('warehouses.update')
                                    <a href="{{ route('warehouses.edit', $w) }}" class="text-pacis-600 hover:underline">Editar</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Sin resultados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">{{ $warehouses->links() }}</div>
    </div>
</div>
