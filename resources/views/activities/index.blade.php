<x-app-layout>
    <x-slot name="title">
        {{ __('Activities') }}
    </x-slot>
    <x-slot name="header">
        <x-header-title>
            {{ __('Activities') }}
        </x-header-title>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('activities.listing', ['user' => $user], 'activities-listing')
        </div>
    </div>
</x-app-layout>