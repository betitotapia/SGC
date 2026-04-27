<div class="space-y-6">
    <div class="flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Clientes</h1>
            <p class="text-sm text-gray-500">Clientes con datos fiscales y vendedor asignado.</p>
        </div>
        @can('customers.create')
            <a href="{{ route('customers.create') }}" class="btn-primary">+ Nuevo cliente</a>
        @endcan
    </div>

    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="bg-white shadow-sm rounded-lg border border-gray-100">
        <div class="p-4 flex gap-3 flex-wrap">
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
                        <th class="px-4 py-3 text-left">Vendedor</th>
                        <th class="px-4 py-3 text-right">Crédito</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($customers as $c)
                        <tr wire:key="c-{{ $c->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono">{{ $c->code }}</td>
                            <td class="px-4 py-3 font-medium">{{ $c->display_name }}</td>
                            <td class="px-4 py-3 font-mono">{{ $c->fiscalProfile?->rfc }}</td>
                            <td class="px-4 py-3">{{ $c->fiscalProfile?->legal_name }}</td>
                            <td class="px-4 py-3">{{ $c->seller?->name }}</td>
                            <td class="px-4 py-3 text-right">${{ number_format($c->credit_limit, 2) }} / {{ $c->credit_days }}d</td>
                            <td class="px-4 py-3 text-right">
                                @can('customers.update')
                                    <a href="{{ route('customers.edit', $c) }}" class="text-pacis-600 hover:underline">Editar</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Sin resultados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $customers->links() }}</div>
    </div>
</div>
