<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'PACIS') }} · PACIS</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen flex">
        <aside class="hidden md:flex md:w-64 bg-pacis-900 text-pacis-100 flex-col">
            <div class="h-16 flex items-center px-6 border-b border-pacis-800">
                <span class="text-xl font-bold text-white">PACIS</span>
                <span class="ml-2 text-xs text-pacis-300">v1.0</span>
            </div>
            <nav class="flex-1 py-4 px-3 space-y-1 text-sm">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    Dashboard
                </x-nav-link>

                @can('products.view')
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">Productos</x-nav-link>
                @endcan
                @can('warehouses.view')
                    <x-nav-link :href="route('warehouses.index')" :active="request()->routeIs('warehouses.*')">Almacenes</x-nav-link>
                @endcan
                @can('customers.view')
                    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">Clientes</x-nav-link>
                @endcan
                @can('suppliers.view')
                    <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">Proveedores</x-nav-link>
                @endcan

                <div class="pt-3 mt-3 border-t border-pacis-800"></div>

                @role('admin')
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">Usuarios</x-nav-link>
                @endrole
            </nav>
            <div class="p-4 text-xs text-pacis-300 border-t border-pacis-800">
                Conectado como<br>
                <strong class="text-white">{{ auth()->user()->name }}</strong>
            </div>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
                <div class="md:hidden font-bold text-pacis-800">PACIS</div>
                <div class="ml-auto flex items-center gap-4">
                    <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-pacis-700">
                        {{ auth()->user()->name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm text-gray-500 hover:text-red-600">Salir</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
