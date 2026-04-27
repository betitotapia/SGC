<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500">Resumen general del sistema.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <p class="text-xs uppercase text-gray-500">Productos activos</p>
            <p class="text-3xl font-semibold text-pacis-700 mt-1">{{ $counters['products'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <p class="text-xs uppercase text-gray-500">Almacenes</p>
            <p class="text-3xl font-semibold text-pacis-700 mt-1">{{ $counters['warehouses'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <p class="text-xs uppercase text-gray-500">Clientes activos</p>
            <p class="text-3xl font-semibold text-pacis-700 mt-1">{{ $counters['customers'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <p class="text-xs uppercase text-gray-500">Remisiones del mes</p>
            <p class="text-3xl font-semibold text-pacis-700 mt-1">{{ $counters['remissions'] }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold">Próximos a caducar</h2>
                <p class="text-xs text-gray-500">Lotes con fecha de caducidad cercana.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Producto</th>
                            <th class="px-4 py-2 text-left">Almacén</th>
                            <th class="px-4 py-2 text-left">Lote</th>
                            <th class="px-4 py-2 text-right">Caduca</th>
                            <th class="px-4 py-2 text-right">Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($expiringSoon as $s)
                            <tr>
                                <td class="px-4 py-2">{{ $s->product?->description }}</td>
                                <td class="px-4 py-2 font-mono text-xs">{{ $s->warehouse?->code }}</td>
                                <td class="px-4 py-2 font-mono text-xs">{{ $s->lot?->lot_number }}</td>
                                <td class="px-4 py-2 text-right">{{ $s->lot?->expires_at?->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 text-right">{{ (int) $s->quantity }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Sin alertas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold">Stock bajo mínimo</h2>
                <p class="text-xs text-gray-500">Productos por debajo de su stock mínimo.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-2 text-left">Referencia</th>
                            <th class="px-4 py-2 text-left">Descripción</th>
                            <th class="px-4 py-2 text-right">Mín.</th>
                            <th class="px-4 py-2 text-right">Existencia</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($lowStock as $p)
                            <tr>
                                <td class="px-4 py-2 font-mono">{{ $p->reference }}</td>
                                <td class="px-4 py-2">{{ $p->description }}</td>
                                <td class="px-4 py-2 text-right text-gray-500">{{ $p->min_stock }}</td>
                                <td class="px-4 py-2 text-right font-semibold text-red-600">{{ (int) $p->availableStock() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Sin alertas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
