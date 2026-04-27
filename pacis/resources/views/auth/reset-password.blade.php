<x-layouts.guest>
    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div>
            <label class="label">Correo</label>
            <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus class="input">
        </div>
        <div>
            <label class="label">Nueva contraseña</label>
            <input type="password" name="password" required class="input">
            @error('password') <p class="text-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" required class="input">
        </div>
        <button class="btn-primary w-full">Restablecer</button>
    </form>
</x-layouts.guest>
