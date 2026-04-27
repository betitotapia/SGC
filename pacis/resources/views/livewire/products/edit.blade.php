<div class="max-w-4xl mx-auto space-y-6" x-data="{ scanning: false }">
    <h1 class="text-2xl font-semibold text-gray-900">
        {{ $product?->exists ? 'Editar producto' : 'Nuevo producto' }}
    </h1>

    <form wire:submit="save" class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-6">
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="label">Referencia *</label>
                <input wire:model="reference" class="input" maxlength="60">
                @error('reference') <p class="text-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Clave alterna</label>
                <input wire:model="alt_key" class="input" maxlength="60">
            </div>

            <div class="md:col-span-2">
                <label class="label">Código de barras</label>
                <div class="flex gap-2">
                    <input wire:model="barcode" class="input font-mono" maxlength="64"
                        placeholder="Escanea o escribe. Se generará uno si lo dejas vacío.">
                    <button type="button" class="btn-ghost" wire:click="generateBarcode">Generar</button>
                    <button type="button" class="btn-ghost" @click="scanning = !scanning" x-text="scanning ? 'Detener' : 'Escanear'"></button>
                </div>
                @if ($barcode_generated)
                    <p class="text-xs text-gray-500 mt-1">Código generado automáticamente por el sistema.</p>
                @endif
                @error('barcode') <p class="text-error">{{ $message }}</p> @enderror

                <div x-show="scanning" x-cloak class="mt-3"
                     x-init="$watch('scanning', value => { if (value) { window.PacisBarcode?.start($refs.video, code => { $wire.set('barcode', code); scanning = false }); } else { window.PacisBarcode?.stop(); } })">
                    <video x-ref="video" class="w-full max-w-md rounded border border-gray-200" autoplay muted playsinline></video>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="label">Descripción *</label>
                <input wire:model="description" class="input">
                @error('description') <p class="text-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Marca</label>
                <input wire:model="brand" class="input">
            </div>
            <div>
                <label class="label">Presentación</label>
                <input wire:model="presentation" class="input" placeholder="Caja 100 pzas">
            </div>

            <div>
                <label class="label">Categoría</label>
                <select wire:model="category_id" class="input">
                    <option value="">—</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Unidad</label>
                <select wire:model="unit_id" class="input">
                    <option value="">—</option>
                    @foreach ($units as $u)
                        <option value="{{ $u->id }}">{{ $u->code }} — {{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label">Costo</label>
                <input type="number" step="0.0001" wire:model="cost" class="input">
            </div>
            <div>
                <label class="label">Precio</label>
                <input type="number" step="0.0001" wire:model="price" class="input">
            </div>
            <div>
                <label class="label">IVA</label>
                <input type="number" step="0.0001" wire:model="tax_rate" class="input" placeholder="0.16">
            </div>
            <div>
                <label class="label">Clave SAT (c_ClaveProdServ)</label>
                <input wire:model="sat_product_code" class="input" maxlength="10">
            </div>

            <div>
                <label class="label">Stock mín.</label>
                <input type="number" wire:model="min_stock" class="input">
            </div>
            <div>
                <label class="label">Stock máx.</label>
                <input type="number" wire:model="max_stock" class="input">
            </div>

            <div class="md:col-span-2 grid grid-cols-2 gap-3 pt-2">
                <label class="inline-flex items-center gap-2"><input type="checkbox" wire:model="requires_lot" class="rounded"> Maneja lote</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" wire:model="requires_expiry" class="rounded"> Maneja caducidad</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" wire:model="controlled" class="rounded"> Controlado (SSA)</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" wire:model="active" class="rounded"> Activo</label>
            </div>

            <div class="md:col-span-2">
                <label class="label">Imagen (opcional)</label>
                <input type="file" wire:model="image" accept="image/*" class="input">
                @error('image') <p class="text-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('products.index') }}" class="btn-ghost">Cancelar</a>
            <button class="btn-primary">Guardar</button>
        </div>
    </form>
</div>
