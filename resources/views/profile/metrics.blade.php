<x-app-layout>
    <x-slot name="header">
        <x-header-title>
            {{ __('Metrics') }}
        </x-header-title>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('user.metrics-form', ['user' => $user])
        </div>
    </div>
</x-app-layout>
