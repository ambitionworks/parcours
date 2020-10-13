@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-2 border-b-4 border-white text-sm font-medium leading-5 text-gray-100 focus:outline-none focus:border-blue-300 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-2 border-b-4 border-transparent text-sm font-medium leading-5 text-gray-50 hover:text-blue-100 hover:border-blue-700 focus:outline-none focus:text-white focus:border-blue-300 transition duration-150 ease-in-out';
@endphp
<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
