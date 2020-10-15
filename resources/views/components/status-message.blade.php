@props(['type' => 'success', 'title' => false, 'resolve' => false])

@php
    switch ($type) {
        case 'error':
            $color = 'red';
            break;
        case 'warning':
            $color = 'yellow';
            break;
        default:
            $color = 'green';
            break;
    }
    // Fool the CSS purger
    // <div class="text-red-600 bg-red-100 border-red-300" />
    // <div class="text-yellow-600 bg-yellow-100 border-yellow-300" />
    // <div class="text-green-600 bg-green-100 border-green-300" />
@endphp

<div {{ $attributes->merge(['class' => "flex space-x-4 mb-4 px-4 py-2 text-$color-600 bg-$color-100"]) }}>
    <div class="flex items-center justify-center animate-pulse bg-white rounded-full w-8 h-8 border-4 border-{{ $color }}-300">
        <svg viewBox="0 0 20 20" fill="currentColor" class="w-8 h-8">
            @switch($type)
                @case('error')
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    @break
                @case('warning')
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    @break
                @case('success')
                @default
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            @endswitch
        </svg>
    </div>
    <div class="w-full mt-1 text-sm align-middle">
        @if ($title)
        <div class="text-base font-semibold leading-none mb-1">{{ $title }}</div>
        @endif
        <div class="leading-tight">{{ $slot }}</div>
        @if ($resolve)
        <a class="block mt-2 font-bold" href="{{ $resolve }}">
            <svg class="inline-block mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
            {{ __('Resolve issue') }}
        </a>
        @endif
    </div>
</div>