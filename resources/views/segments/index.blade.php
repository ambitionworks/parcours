<x-app-layout>
    <x-slot name="header">
        <div class="flex w-full items-center">
            <x-header-title class="flex-1">
                {{ __('Segments') }}
            </x-header-title>
        </div>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white pb-2 shadow-xl sm:rounded-lg">
                @livewire('segments.search')
            </div>
        </div>
    </div>
</x-app-layout>
