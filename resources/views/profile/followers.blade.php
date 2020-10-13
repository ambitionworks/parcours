<x-app-layout>
    <x-slot name="header">
        <x-header-title>
            {{ __('Followers') }}
        </x-header-title>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('user.follower-preference-form', ['user' => $user])
            <x-jet-section-border />

            @if ($user->follow_requests_received()->count())
            @livewire('user.follower-requests-form', ['user' => $user])
            <x-jet-section-border />
            @endif

        </div>
    </div>
</x-app-layout>
