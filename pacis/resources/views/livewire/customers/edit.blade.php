<div class="max-w-5xl mx-auto space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900">
        {{ $customer?->exists ? 'Editar cliente' : 'Nuevo cliente' }}
    </h1>

    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6">
            <h2 class="font-semibold mb-4">Constancia de Situación Fiscal (CSF)</h2>
            <div class="grid md:grid-cols-3 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="label">PDF de la CSF (SAT)</label>
                    <input type="file" wire:model="csf_file" accept="application/pdf" class="input">
                    @error('csf_file') <p class="text-error">{{ $message }}</p> @enderror
                    @if ($existing_csf_path)
                        <p class="text-xs text-gray-500 mt-1">Archivo actual: {{ basename($existing_csf_path) }}</p>
                    @endif
                </div>
                <div>
                    <button type="button" wire:click="parseCsf" class="btn-ghost w-full"
                        wire:loading.attr="disabled" wire:target="parseCsf,csf_file">
                        <span wire:loading.remove wire:target="parseCsf">Leer CSF y autollenar</span>
                        <span wire:loading wire:target="parseCsf">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-4">
            <h2 class="font-semibold">Datos fiscales</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="label">RFC</label>
                    <input wire:model="rfc" class="input font-mono uppercase" maxlength="13">
                    @error('rfc') <p class="text-error">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="label">Razón social</label>
                    <input wire:model="legal_name" class="input">
                </div>
                <div>
                    <label class="label">Nombre comercial</label>
                    <input wire:model="commercial_name" class="input">
                </div>
                <div>
                    <label class="label">Régimen (clave)</label>
                    <input wire:model="tax_regime_code" class="input" maxlength="10">
                </div>
                <div>
                    <label class="label">Régimen (nombre)</label>
                    <input wire:model="tax_regime_name" class="input">
                </div>
                <div>
                    <label class="label">Uso de CFDI</label>
                    <input wire:model="cfdi_use" class="input" placeholder="G03">
                </div>
                <div>
                    <label class="label">CP</label>
                    <input wire:model="zip" class="input" maxlength="10">
                </div>
                <div>
                    <label class="label">Email fiscal</label>
                    <input wire:model="fiscal_email" class="input" type="email">
                </div>
                <div class="md:col-span-3">
                    <label class="label">Domicilio</label>
                    <div class="grid md:grid-cols-5 gap-2">
                        <input wire:model="street" class="input md:col-span-2" placeholder="Calle">
                        <input wire:model="exterior_number" class="input" placeholder="Ext.">
                        <input wire:model="interior_number" class="input" placeholder="Int.">
                        <input wire:model="neighborhood" class="input" placeholder="Colonia">
                    </div>
                </div>
                <div>
                    <label class="label">Municipio</label>
                    <input wire:model="municipality" class="input">
                </div>
                <div>
                    <label class="label">Estado</label>
                    <input wire:model="state" class="input">
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-4">
            <h2 class="font-semibold">Datos comerciales</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="label">Código *</label>
                    <input wire:model="code" class="input">
                    @error('code') <p class="text-error">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="label">Nombre para mostrar *</label>
                    <input wire:model="display_name" class="input">
                    @error('display_name') <p class="text-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Contacto</label>
                    <input wire:model="contact_name" class="input">
                </div>
                <div>
                    <label class="label">Email</label>
                    <input wire:model="email" class="input" type="email">
                </div>
                <div>
                    <label class="label">Teléfono</label>
                    <input wire:model="phone" class="input">
                </div>
                <div>
                    <label class="label">Vendedor asignado</label>
                    <select wire:model="seller_id" class="input">
                        <option value="">—</option>
                        @foreach ($sellers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Límite de crédito</label>
                    <input type="number" step="0.01" wire:model="credit_limit" class="input">
                </div>
                <div>
                    <label class="label">Días de crédito</label>
                    <input type="number" wire:model="credit_days" class="input">
                </div>
                <div>
                    <label class="label">Lista de precios</label>
                    <input wire:model="price_list" class="input">
                </div>
                <div class="md:col-span-3">
                    <label class="label">Notas</label>
                    <textarea wire:model="notes" rows="2" class="input"></textarea>
                </div>
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" wire:model="active" class="rounded"> Activo
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('customers.index') }}" class="btn-ghost">Cancelar</a>
            <button class="btn-primary">Guardar</button>
        </div>
    </form>
</div>
