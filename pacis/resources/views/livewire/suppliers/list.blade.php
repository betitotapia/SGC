<div class="space-y-6">
    <div class="flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Proveedores</h1>
            <p class="text-sm text-gray-500">Proveedores y datos fiscales para órdenes de compra.</p>
        </div>
        @can('suppliers.create')
            <a href="{{ route('suppliers.create') }}" class="btn-primary">+ Nuevo proveedor</a>
        @endcan
    </div>

    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="bg-white shadow-sm rounded-lg border border-gray-100">
        <div class="p-4">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Buscar por nombre, código o RFC..." class="input w-full md:w-[420px]">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Código</th>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">RFC</th>
                        <th class="px-4 py-3 text-left">Razón social</th>
                        <th class="px-4 py-3 text-right">Lead time</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($suppliers as $s)
                        <tr wire:key="s-{{ $s->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono">{{ $s->code }}</td>
                            <td class="px-4 py-3 font-medium">{{ $s->display_name }}</td>
                            <td class="px-4 py-3 font-mono">{{ $s->fiscalProfile?->rfc }}</td>
                            <td class="px-4 py-3">{{ $s->fiscalProfile?->legal_name }}</td>
                            <td class="px-4 py-3 text-right">{{ $s->lead_time_days }}d</td>
                            <td class="px-4 py-3 text-right">
                                @can('suppliers.update')
                                    <a href="{{ route('suppliers.edit', $s) }}" class="text-pacis-600 hover:underline">Editar</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Sin resultados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $suppliers->links() }}</div>
    </div>
</div>
