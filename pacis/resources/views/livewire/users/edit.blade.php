<div class="max-w-2xl mx-auto space-y-6">
    <h1 class="text-2xl font-semibold text-gray-900">
        {{ $user?->exists ? 'Editar usuario' : 'Nuevo usuario' }}
    </h1>

    <form wire:submit="save" class="bg-white shadow-sm rounded-lg border border-gray-100 p-6 space-y-5">
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="label">Nombre *</label>
                <input wire:model="name" class="input">
                @error('name') <p class="text-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Email *</label>
                <input wire:model="email" class="input" type="email">
                @error('email') <p class="text-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Teléfono</label>
                <input wire:model="phone" class="input">
            </div>
            <div>
                <label class="label">Estatus</label>
                <label class="inline-flex items-center gap-2 mt-2">
                    <input type="checkbox" wire:model="active" class="rounded"> Activo
                </label>
            </div>
            <div>
                <label class="label">{{ $user ? 'Nueva contraseña (opcional)' : 'Contraseña *' }}</label>
                <input wire:model="password" type="password" class="input">
                @error('password') <p class="text-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Confirmar contraseña</label>
                <input wire:model="password_confirmation" type="password" class="input">
            </div>
            <div class="md:col-span-2">
                <label class="label">Roles</label>
                <div class="grid md:grid-cols-2 gap-2">
                    @foreach ($availableRoles as $role)
                        <label class="inline-flex items-center gap-2 border border-gray-200 rounded px-3 py-2 bg-gray-50">
                            <input type="checkbox" wire:model="roles" value="{{ $role }}" class="rounded"> {{ $role }}
                        </label>
                    @endforeach
                </div>
                @error('roles') <p class="text-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('users.index') }}" class="btn-ghost">Cancelar</a>
            <button class="btn-primary">Guardar</button>
        </div>
    </form>
</div>
