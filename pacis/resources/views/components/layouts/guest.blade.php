<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PACIS') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-pacis-50 to-white min-h-screen">
    <div class="min-h-screen flex flex-col items-center justify-center p-6">
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-pacis-800">PACIS</h1>
            <p class="text-sm text-gray-500">Sistema de remisión de insumos médicos</p>
        </div>
        <div class="w-full sm:max-w-md bg-white shadow-md rounded-lg border border-gray-100 p-6">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
