<x-layouts.guest>
    @if (session('status'))
        <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="label">Correo</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="input">
            @error('email') <p class="text-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label">Contraseña</label>
            <input type="password" name="password" required class="input">
            @error('password') <p class="text-error">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" class="rounded"> Recordarme
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-pacis-600 hover:underline">¿Olvidaste tu contraseña?</a>
            @endif
        </div>
        <button class="btn-primary w-full">Ingresar</button>
    </form>
</x-layouts.guest>
