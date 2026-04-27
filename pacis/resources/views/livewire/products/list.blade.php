<div class="space-y-6">
    <div class="flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Productos</h1>
            <p class="text-sm text-gray-500">Insumos médicos y su catálogo base.</p>
        </div>
        @can('products.create')
            <a href="{{ route('products.create') }}" class="btn-primary">+ Nuevo producto</a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow-sm rounded-lg border border-gray-100">
        <div class="p-4 flex gap-3 flex-wrap">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Buscar por referencia, clave alterna, código de barras o descripción..."
                class="input w-full md:w-[480px]">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Referencia</th>
                        <th class="px-4 py-3 text-left">Clave alt.</th>
                        <th class="px-4 py-3 text-left">Código de barras</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-right">Precio</th>
                        <th class="px-4 py-3 text-right">Stock</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($products as $p)
                        <tr wire:key="p-{{ $p->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-pacis-700">{{ $p->reference }}</td>
                            <td class="px-4 py-3 font-mono text-gray-500">{{ $p->alt_key }}</td>
                            <td class="px-4 py-3 font-mono text-xs">
                                {{ $p->barcode }}
                                @if ($p->barcode_generated)
                                    <span class="badge badge-gray ml-1">gen</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $p->description }}</div>
                                <div class="text-xs text-gray-500">{{ $p->brand }} · {{ $p->presentation }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">${{ number_format($p->price, 2) }}</td>
                            <td class="px-4 py-3 text-right">{{ (int) $p->stocks()->sum('quantity') }}</td>
                            <td class="px-4 py-3 text-right">
                                @can('products.update')
                                    <a href="{{ route('products.edit', $p) }}" class="text-pacis-600 hover:underline">Editar</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Sin resultados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $products->links() }}</div>
    </div>
</div>
