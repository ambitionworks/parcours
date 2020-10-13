<x-app-layout>
    <x-slot name="title">
        {{ $viewingUser->name }}
    </x-slot>
    <x-slot name="header">
        <div class="flex w-full items-center">
            <x-user-profile-photo class="inline-flex mr-4" size="large" :user="$viewingUser" />
            <x-header-title class="flex-1">
                {{ $viewingUser->name }}
            </x-header-title>
            <div class="divide-x">
                @livewire('user.follower-actions', ['user' => $user, 'viewingUser' => $viewingUser])
            </div>
        </div>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <h2 class="mb-3 text-blue-100 text-3xl font-bold">{{ __('Most Recent') }}</h2>
            <x-activities.list :activities="$activity" :showUser="false" />

            {{-- <x-jet-section-border /> --}}

        </div>
    </div>
</x-app-layout>
