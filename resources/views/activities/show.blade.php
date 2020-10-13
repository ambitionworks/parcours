@php
    $default_tab = !$activity->stationary ? 'segments' : 'analysis';
@endphp

<x-slot name="title">
    {{ $activity->name }}
</x-slot>
<x-slot name="header">
    <div class="flex w-full">
        <h2 class="flex flex-col justify-center flex-grow font-semibold text-xl text-white leading-tight">
            @can('update', $activity)
                {{-- @todo This is inexplicably broken using Livewire --}}
                <div x-data="{ editing: false }" @activity-edit.window="editing = !editing">
                    <div class="flex flex-col" x-show="!editing" @click="$dispatch('activity-edit')">
                        <div class="relative group cursor-pointer">
                            <span class="invisible group-hover:visible absolute left-0 inset-y-0 -ml-6 flex flex-col justify-center text-blue-600">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </span>
                            {{ $name }}
                        </div>
                        <div class="mt-2 text-sm font-normal">
                            @datetime($activity->performed_at_tz)
                        </div>
                    </div>
                    <form method="POST" action="{{ route('activities.update', $activity) }}" x-show="editing" class="inline align-middle">
                        @csrf
                        @method('PUT')
                        <x-jet-input type="text" class="w-64 text-sm text-gray-900" name="name" value="{{ $name }}" {{-- wire:model="name" --}} />
                        <x-jet-secondary-button class="text-sm" @click="$dispatch('activity-edit')">
                            Cancel
                        </x-jet-secondary-button>
                        <x-jet-button class="text-sm">
                            Save
                        </x-jet-button>
                    </form>
                </div>
            @else
                <div>{{ $activity->name }}</div>
                <div class="mt-2 text-sm font-normal">
                    <a href="{{ route('user.profile', $activity->user) }}">
                        <x-user-profile-photo :user="$activity->user" class="inline-flex mr-2" /> {{ $activity->user->name }}
                    </a> {{ __('on') }}
                    @datetime($activity->performed_at_tz)
                </div>
            @endcan
        </h2>
        <div class="flex flex-grow-0 divide-x divide-gray-300 text-white font-semibold text-sm z-40">
            @if ($activity->processed_at)
                <div title="{{ __('Active Duration') }}" class="flex flex-col justify-center items-center px-4">
                    <span class="py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Moving time') }}</span>
                    <span>{{ gmdate('H:i:s', $activity->active_duration) }}</span>
                </div>
                <div title="{{ __('Distance') }}" class="flex flex-col justify-center items-center px-4">
                    <span class="py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Distance') }}</span>
                    <span>{{ $activity->distance }} KM</span>
                </div>
                @if ($activity->ascent)
                <div title="{{ __('Elevation Gain') }}" class="flex flex-col justify-center items-center px-4">
                    <span class="py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700"><abbr title="{{ __('Elevation Gain') }}">{{ __('Ele. Gain') }}</abbr></span>
                    <span>{{ $activity->ascent }} M</span>
                </div>
                @endif
            @endif
            <div class="flex flex-col justify-center items-center px-4">
                @livewire('activities.likers', ['activity' => $activity])
            </div>
            <div class="flex flex-col justify-center items-center px-4">
                <a class="text-center hover:text-blue-700" href="#comments">
                    <svg class="h-6 w-6 mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    {{ $activity->comments->count() }}
                </a>
            </div>
            @livewire('activities.actions', ['activity' => $activity])
        </div>
    </div>
</x-slot>

<div class="{{ $activity->description ? 'py-8' : 'py-12' }}">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div x-data="{ editing: @entangle('editing') }" @activity-edit.window="editing = !editing">
            @if ($activity->description)
            <blockquote x-show="!editing" class="text-blue-100 italic -mb-1 bg-gradient-to-b from-gray-900 to-black px-8 py-4 rounded-lg">{{ $activity->description }}</blockquote>
            @endif
            @can('update', $activity)
            <div class="mb-2 p-5 rounded-lg bg-gradient-to-b from-gray-900 via-gray-900 to-black" x-show="editing">
                <textarea wire:model="description" class="appearance-none italic w-full bg-transparent text-blue-100 placeholder-blue-100 focus:outline-none" placeholder="{{ __('Add a description ...') }}" rows="3"></textarea>
                <div class="flex mt-2 justify-end space-x-2">
                    <x-jet-secondary-button class="text-sm" @click="$dispatch('activity-edit')">
                        Cancel
                    </x-jet-secondary-button>
                    <x-jet-button wire:click="updateDescription" @click="$dispatch('activity-edit')" class="text-sm">
                        Save
                    </x-jet-button>
                </div>
            </div>
            @endcan
        </div>
        <div class="bg-white pb-2 shadow-xl sm:rounded-lg">
            <div class="w-full space-y-4 mb-4">
                <x-activities.map-graph wire:ignore :activity="$activity" />

                <x-tab-list default="{{ $default_tab }}" class="px-5">
                    <x-slot name="tabs">
                        @if (!$activity->stationary)
                            <x-tab-list-tab for="segments" title="{{ __('Segments') }}" />
                        @endif
                        @if ($activity->has_laps)
                            <x-tab-list-tab for="laps" title="{{ __('Laps') }}" />
                        @endif
                        <x-tab-list-tab for="analysis" title="{{ __('Analysis') }}" />
                    </x-slot>
                    <x-slot name="contents">
                        @if (!$activity->stationary)
                            <x-tab-list-content for="segments">
                                @if (!$activity->processed_at)
                                <x-loading-table></x-loading-table>
                                @else
                                <table x-data class="min-w-full shadow rounded border-b border-gray-200 divide-y divide-gray-200">
                                    <thead class="bg-gray-100 text-left text-xs uppercase">
                                        <th></th>
                                        <th class="px-6 py-3 font-medium tracking-wider leading-4 text-gray-400">
                                            {{ __('Segment Name') }}
                                        </th>
                                        <th class="px-6 py-3 font-medium tracking-wider leading-4 text-gray-400 text-center">
                                            {{ __('This Activity') }}
                                        </th>
                                        <th class="px-6 py-3 font-medium tracking-wider leading-4 text-gray-400 text-center">
                                            {{ __('Personal Best') }}
                                        </th>
                                        </th>
                                        <th class="px-6 py-3 font-medium tracking-wider leading-4 text-gray-400">
                                            {{ __('Performance') }}
                                        </th>
                                        <th class="px-6 py-3 font-medium tracking-wider leading-4 text-gray-300 flex justify-center">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
                                            </svg>
                                        </th>
                                    </thead>
                                    <tbody class="text-sm text-gray-700">
                                        @forelse ($activity->segments as $segment)
                                        <tr @click="$dispatch('map-segment', { id: {{ $segment->id }}, start: {{ $segment->pivot->start_time }}, end: {{ $segment->pivot->end_time }} })" class="cursor-pointer {{ $loop->index % 2 !== 0 ? 'bg-gray-100 hover:bg-gray-200' : 'hover:bg-gray-200' }}">
                                                <td class="pl-2">
                                                    @livewire('segments.favourite', ['segment' => $segment])
                                                </td>
                                                <td class="px-6 py-3 flex flex-col space-y-1">
                                                    <div class="font-medium">{{ $segment->name }}</div>
                                                    <div class="text-xs text-gray-500 flex space-x-2">
                                                        @if ($segment->distance)
                                                        <span>
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="inline w-4 h-4" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M12 1.586l-4 4v12.828l4-4V1.586zM3.707 3.293A1 1 0 002 4v10a1 1 0 00.293.707L6 18.414V5.586L3.707 3.293zM17.707 5.293L14 1.586v12.828l2.293 2.293A1 1 0 0018 16V6a1 1 0 00-.293-.707z" clip-rule="evenodd" />
                                                            </svg>
                                                            {{ $segment->distance }} km
                                                        </span>
                                                        @endif
                                                        @if ($segment->altitude_change)
                                                        <span>
                                                            @if ($segment->altitude_change > 0)
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="inline w-4 h-4" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                                                            </svg>
                                                            @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="inline w-4 h-4" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
                                                            </svg>
                                                            @endif
                                                            {{ $segment->altitude_change }} m
                                                            @if ($segment->altitude_change && $segment->distance)
                                                            ({{ round(($segment->altitude_change / ($segment->distance * 1000)) * 100) }}%)
                                                            @endif
                                                        </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-3 text-center">
                                                    @if ($segment_records[$segment->id][0]->elapsed === ($segment->pivot->end_time - $segment->pivot->start_time))
                                                    <span class="inline-block px-3 py-1 rounded-lg shadow-sm font-semibold bg-yellow-200 text-yellow-900">🥇
                                                    @elseif ($segment_records[$segment->id][1]->elapsed === ($segment->pivot->end_time - $segment->pivot->start_time))
                                                    <span class="inline-block px-3 py-1 rounded-lg shadow-sm font-semibold bg-gray-200 text-gray-900">🥈
                                                    @elseif ($segment_records[$segment->id][2]->elapsed === ($segment->pivot->end_time - $segment->pivot->start_time))
                                                    <span class="inline-block px-3 py-1 rounded-lg shadow-sm font-semibold bg-orange-700 text-white">🥉
                                                    @else
                                                    <span>
                                                    @endif
                                                    {{ gmdate('H:i:s', ($segment->pivot->end_time - $segment->pivot->start_time)) }}</span>
                                                </td>
                                                <td class="px-6 py-3 text-center">
                                                    @if ($segment_records[$segment->id][0]->activity_id !== $activity->id)
                                                    <a class="inline-block px-3 py-1 rounded-lg bg-blue-50 border-b border-blue-300" href="{{ route('activities.show', $segment_records[$segment->id][0]->activity_id) }}">
                                                        {{ gmdate('H:i:s', $segment_records[$segment->id][0]->elapsed) }}
                                                    </a>
                                                    @else
                                                    <span class="px-2 py-1">{{ gmdate('H:i:s', $segment_records[$segment->id][0]->elapsed) }}</span>
                                                    @endif
                                                </td>
                                                <td class="flex px-6 py-3 space-x-3">
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700"><abbr title="{{ __('Average heart rate') }}">{{ __('Avg. HR') }}</abbr></span>
                                                        <span class="text-sm font-medium">{{ round($segment->pivot->avg_hr) }} BPM</span>
                                                    </div>
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Avg. Power') }}</span>
                                                        <span class="text-sm font-medium">{{ round($segment->pivot->avg_power) }} W</span>
                                                    </div>
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Avg. Speed') }}</span>
                                                        <span class="text-sm font-medium">{{ round($segment->pivot->avg_speed, 1) }} km/h</span>
                                                    </div>
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Avg. Cadence') }}</span>
                                                        <span class="text-sm font-medium">{{ round($segment->pivot->avg_cadence) }} RPM</span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-3">
                                                    <a href="{{ route('activities.segment', [$activity, $segment, $segment->pivot->start_time]) }}"><x-jet-button>{{ __('View') }}</x-jet-button></a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="px-6 py-3" colspan="4">No segments match this activity. Perhaps you'd like to <a class="px-1 pb-1 rounded-full bg-blue-100 text-blue-700 font-semibold" href="{{ route('activities.segments.create', $activity) }}">create one</a>?</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @endif
                            </x-tab-list-content>
                        @endif
                        @if ($activity->has_laps)
                            <x-tab-list-content for="laps">
                                <table class="min-w-full shadow rounded border-b border-gray-200 divide-y divide-gray-200">
                                    <thead class="bg-gray-100 text-left text-xs uppercase">
                                        <th class="px-6 py-3 font-medium tracking-wider leading-4 text-gray-400">
                                            {{ __('Lap') }}
                                        </th>
                                        <th class="px-6 py-3 font-medium tracking-wider leading-4 text-gray-400">
                                            {{ __('Start time') }}
                                        </th>
                                        <th class="px-6 py-3 font-medium tracking-wider leading-4 text-gray-400">
                                            {{ __('Duration') }}
                                        </th>
                                        </th>
                                        <th class="px-6 py-3 font-medium tracking-wider leading-4 text-gray-400">
                                            {{ __('Performance') }}
                                        </th>
                                    </thead>
                                    <tbody x-data class="text-sm text-gray-700">
                                        @foreach ($laps as $lap)
                                        <tr @click="$dispatch('map-lap', { id: {{ $loop->index }}, start: {{ $lap['start_time'] }}, end: {{ $lap['end_time'] }} })" class="cursor-pointer {{ $loop->index % 2 !== 0 ? 'bg-gray-100 hover:bg-gray-200' : 'hover:bg-gray-200' }}">
                                                <td class="px-6 py-3">
                                                    <div class="font-semibold">
                                                        {{ __('Lap').' #'.($loop->index+1) }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-3">
                                                    {{ gmdate('H:i:s', $lap['start_time'] - strtotime($activity->performed_at)) }}
                                                </td>
                                                <td class="px-6 py-3">
                                                    {{ gmdate('H:i:s', $lap['end_time'] - $lap['start_time']) }}
                                                </td>
                                                <td class="flex px-6 py-3 space-x-3">
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700"><abbr title="{{ __('Average heart rate') }}">{{ __('Avg. HR') }}</abbr></span>
                                                        <span class="text-sm font-medium">{{ round($lap['avg_hr']) }} BPM</span>
                                                    </div>
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Avg. Power') }}</span>
                                                        <span class="text-sm font-medium">{{ round($lap['avg_power']) }} W</span>
                                                    </div>
                                                    {{-- <div class="flex flex-col justify-center items-center">
                                                        <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700"><abbr title="{{ __('Normalized power') }}">{{ __('NP') }}</abbr></span>
                                                        <span class="text-sm font-medium">{{ round($lap['normalized_power']) }} W</span>
                                                    </div> --}}
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Avg. Speed') }}</span>
                                                        <span class="text-sm font-medium">{{ round($lap['avg_speed'], 1) }} km/h</span>
                                                    </div>
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Avg. cadence') }}</span>
                                                        <span class="text-sm font-medium">{{ round($lap['avg_cadence'], 1) }} RPM</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </x-tab-list-content>
                        @endif
                        <x-tab-list-content for="analysis">
                            <div x-data="analysisTab()" @tab-select-analysis.window="init()" {{ $default_tab === 'analysis' ? 'x-init=init()' : '' }}>
                                <div x-show="!initialized">
                                    <x-loading-spinner />
                                    Loading...
                                </div>
                                <div x-show="initialized && data.error === 'no_metrics'">
                                    @if ($activity->user->is(Auth::user()))
                                        <x-status-message type="warning" resolve="{{ route('user.metrics', Auth::user()) }}">
                                            {{ __("Analysis on this activity could not be performed. Are you sure you've entered your metrics?") }}
                                        </x-status-message>
                                    @else
                                        <x-status-message type="warning">
                                            {{ __('Analysis on this activity could not be performed.') }}
                                        </x-status-message>
                                    @endif
                                </div>
                                <div x-show="initialized && data.error === 'no_data'">
                                    <x-status-message type="warning">
                                        {{ __('This activity has no performance data.') }}
                                    </x-status-message>
                                </div>
                                <div x-show="initialized && typeof data.error === 'undefined'" class="flex space-x-5">
                                    <div class="w-1/3">
                                        <h3 class="mb-2 text-lg font-semibold text-gray-900">{{ __('Heart Rate Zones') }}</h3>
                                        <table class="w-full">
                                            <template x-if="initialized && typeof data.error === 'undefined'" x-for="(item, index) in data.hr_partition" :key="index">
                                                <tr class="h-8">
                                                    <td class="w-1/6 text-sm text-gray-800 font-medium align-top" x-text="item.key"></td>
                                                    <td class="w-full text-xs text-gray-900 relative">
                                                        <div :class="{ ['py-3 bg-red-' + ((index + 1) * 100)]: initialized }" :style="'width: ' + item.value + '%';"></div><span x-text="item.value + '%'"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                    </div>
                                    <div class="w-1/3">
                                        <h3 class="mb-2 text-lg font-semibold text-gray-900">{{ __('Power Zones') }}</h3>
                                        <table class="w-full">
                                            <template x-if="initialized && typeof data.error === 'undefined'" x-for="(item, index) in data.power_partition" :key="index">
                                                <tr class="h-8">
                                                    <td class="w-1/6 text-sm text-gray-800 font-medium align-top" x-text="item.key"></td>
                                                    <td class="w-full text-xs text-gray-900 relative">
                                                        <div :class="{ ['py-3 bg-purple-' + ((index + 1) * 100)]: initialized }" :style="'width: ' + item.value + '%';"></div><span x-text="item.value + '%'"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                    </div>
                                    <div class="w-1/3">
                                        <h3 class="mb-2 text-lg font-semibold text-gray-900">{{ __('Power Metrics') }}</h3>
                                        <table class="w-full">
                                            <template x-if="initialized && typeof data.error === 'undefined'" x-for="(item, index) in data.power_metrics" :key="index">
                                                <tr>
                                                    <td class="w-1/2 py-2 text-sm font-medium" x-text="item.key"></td>
                                                    <td class="w-full py-2" x-text="item.value">
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <script>
                                function analysisTab() {
                                    return {
                                        initialized: false,
                                        data: { hr_partition: {} },
                                        init() {
                                            if (this.initialized) return

                                            fetch('{{ route('activities.analysis', $activity) }}').then(res => res.json()).then(data => {
                                                this.data = data
                                                this.initialized = true
                                            })
                                        }
                                    }
                                }
                            </script>
                        </x-tab-list-content>
                    </x-slot>
                </x-tab-list>

                <div id="comments" class="px-5">
                    <h2 class="mb-3 text-2xl font-bold text-gray-900">{{ __('Comments') }}</h2>
                    @livewire('comments.listing', ['model' => $activity])
                </div>
            </div>
        </div>
    </div>
</div>
