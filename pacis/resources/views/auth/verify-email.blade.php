<x-layouts.guest>
    <p class="text-sm text-gray-600 mb-4">
        Gracias por registrarte. Antes de continuar, verifica tu correo electrónico haciendo click en el enlace que te enviamos.
    </p>
    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm text-green-600">Enviamos un nuevo enlace de verificación a tu correo.</div>
    @endif
    <div class="flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn-primary">Reenviar enlace</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-sm text-gray-500 hover:underline">Cerrar sesión</button>
        </form>
    </div>
</x-layouts.guest>
