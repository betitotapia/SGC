<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">
            {{ $warehouse?->exists ? 'Editar almacén' : 'Nuevo almacén' }}
        </h1>
    </div>

    <form wire:submit="save" class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-5">
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="label">Clave *</label>
                <input wire:model="code" class="input" maxlength="20" placeholder="CEDIS, SUC01...">
                @error('code') <p class="text-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Nombre *</label>
                <input wire:model="name" class="input">
                @error('name') <p class="text-error">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="label">Dirección</label>
                <input wire:model="address" class="input">
            </div>

            <div>
                <label class="label">Ciudad</label>
                <input wire:model="city" class="input">
            </div>
            <div>
                <label class="label">Estado</label>
                <input wire:model="state" class="input">
            </div>

            <div>
                <label class="label">CP</label>
                <input wire:model="zip" class="input" maxlength="10">
            </div>
            <div>
                <label class="label">Teléfono</label>
                <input wire:model="phone" class="input">
            </div>
            <div class="md:col-span-2">
                <label class="label">Encargado</label>
                <input wire:model="manager" class="input">
            </div>
            <div class="md:col-span-2">
                <label class="label">Notas</label>
                <textarea wire:model="notes" class="input" rows="3"></textarea>
            </div>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" wire:model="active" class="rounded"> Activo
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" wire:model="is_default" class="rounded"> Marcar como almacén por defecto
            </label>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('warehouses.index') }}" class="btn-ghost">Cancelar</a>
            <button class="btn-primary">Guardar</button>
        </div>
    </form>
</div>
