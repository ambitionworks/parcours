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
            <div class="flex flex-grow-0 divide-x divide-gray-300 text-white font-semibold text-sm">
                <div class="flex flex-col justify-center items-center px-4">
                    <span class="py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Activity') }}</span>
                    <a href="{{ route('activities.show', $activity) }}">{{ $activity->name }}</a>
                </div>
                <div class="flex flex-col justify-center items-center px-4">
                    <span class="py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('This Activity') }}</span>
                    <span>{{ gmdate('H:i:s', ($segment->pivot->end_time - $segment->pivot->start_time)) }}</span>
                </div>
                <div class="flex flex-col justify-center items-center px-4">
                    <span class="py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Personal Best') }}</span>
                    <span>{{ gmdate('H:i:s', $pb->elapsed) }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white pb-2 shadow-xl sm:rounded-lg">
                <div class="w-full space-y-4 mb-4">
                    <x-activities.map-graph :activity="$activity" :start="$segment->pivot->start_time" :end="$segment->pivot->end_time" />

                    <x-tab-list default="efforts" class="px-5">
                        <x-slot name="tabs">
                            <x-tab-list-tab for="efforts" title="{{ $activity->user->is(Auth::user()) ? __('Your Efforts') : __(':name\'s Efforts', ['name' => $request->user()->name]) }}" />
                            <x-tab-list-tab for="leaderboard" title="{{ __('Leaderboard') }}" />
                        </x-slot>
                        <x-slot name="contents">
                            <x-tab-list-content for="efforts">
                                @livewire('segments.activity-user-effort-list', ['user' => $activity->user, 'segment' => $segment])
                            </x-tab-list-content>
                            <x-tab-list-content for="leaderboard">
                                @livewire('segments.leaderboard-list', ['segment' => $segment])
                            </x-tab-list-content>
                        </x-slot>
                    </x-tab-list>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
