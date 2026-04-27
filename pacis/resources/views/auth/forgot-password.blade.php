<x-layouts.guest>
    <p class="text-sm text-gray-600 mb-4">Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>
    @if (session('status'))
        <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label class="label">Correo</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="input">
            @error('email') <p class="text-error">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center justify-between">
            <a href="{{ route('login') }}" class="text-sm text-pacis-600 hover:underline">← Ingresar</a>
            <button class="btn-primary">Enviar enlace</button>
        </div>
    </form>
</x-layouts.guest>
