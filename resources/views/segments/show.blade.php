<x-app-layout>
    <x-slot name="title">
        {{ $segment->name }}
    </x-slot>
    <x-slot name="header">
        <div class="flex w-full items-center">
            <span class="mr-2 text-white">
                @livewire('segments.favourite', ['segment' => $segment])
            </span>
            <x-header-title class="flex-1">
                {{ $segment->name }}
            </x-header-title>
            <div class="divide-x">

            </div>
        </div>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white pb-2 shadow-xl sm:rounded-lg">
                <x-segments.map-graph :geojson="$geojson">
                    <x-tab-list default="leaderboard" class="px-5">
                        <x-slot name="tabs">
                            <x-tab-list-tab for="leaderboard" title="{{ __('Leaderboard') }}" />
                            <x-tab-list-tab for="efforts" title="{{ __('Your Efforts') }}" />
                        </x-slot>
                        <x-slot name="contents">
                            <x-tab-list-content for="efforts">
                                @livewire('segments.activity-user-effort-list', ['user' => Auth::user(), 'segment' => $segment])
                            </x-tab-list-content>
                            <x-tab-list-content for="leaderboard">
                                @livewire('segments.leaderboard-list', ['segment' => $segment])
                            </x-tab-list-content>
                        </x-slot>
                    </x-tab-list>
                </x-segments.map-graph>
            </div>
        </div>
    </div>
</x-app-layout>
