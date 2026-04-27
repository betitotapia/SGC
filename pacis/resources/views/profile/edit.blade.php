<x-layouts.app>
    <div class="max-w-2xl mx-auto space-y-6">
        <h1 class="text-2xl font-semibold text-gray-900">Mi perfil</h1>

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success">Perfil actualizado.</div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold mb-4">Datos personales</h2>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="label">Nombre</label>
                    <input name="name" value="{{ old('name', $user->name) }}" required class="input">
                    @error('name') <p class="text-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Correo</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input">
                    @error('email') <p class="text-error">{{ $message }}</p> @enderror
                </div>
                <button class="btn-primary">Guardar</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold mb-4">Cambiar contraseña</h2>
            @if (session('status') === 'password-updated')
                <div class="alert alert-success">Contraseña actualizada.</div>
            @endif
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="label">Contraseña actual</label>
                    <input type="password" name="current_password" class="input">
                    @error('current_password', 'updatePassword') <p class="text-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Nueva contraseña</label>
                    <input type="password" name="password" class="input">
                    @error('password', 'updatePassword') <p class="text-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Confirmar</label>
                    <input type="password" name="password_confirmation" class="input">
                </div>
                <button class="btn-primary">Actualizar</button>
            </form>
        </div>
    </div>
</x-layouts.app>
