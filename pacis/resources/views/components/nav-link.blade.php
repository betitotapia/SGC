@props(['active' => false])

@php
    $classes = $active
        ? 'block px-3 py-2 rounded-md bg-pacis-700 text-white'
        : 'block px-3 py-2 rounded-md text-pacis-100 hover:bg-pacis-800 hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
