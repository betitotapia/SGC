<x-layouts.guest>
    <p class="text-sm text-gray-600 mb-4">Esta es una zona segura. Confirma tu contraseña antes de continuar.</p>
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf
        <div>
            <label class="label">Contraseña</label>
            <input type="password" name="password" required autofocus class="input">
            @error('password') <p class="text-error">{{ $message }}</p> @enderror
        </div>
        <button class="btn-primary w-full">Confirmar</button>
    </form>
</x-layouts.guest>
