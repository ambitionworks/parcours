@props(['activity', 'showUser' => false])

<div wire:ignore class="h-40 bg-black rounded-lg relative" {{ $attributes }}>
    @if ($showUser)
        <a href="{{ route('user.profile', $activity->user) }}" class="absolute z-20 px-2 pr-3 py-1 top-0 left-0 ml-1 mt-1 rounded-full shadow-sm bg-black text-sm text-blue-100">
            <x-user-profile-photo class="inline-flex mr-2" :user="$activity->user" />
            {{ $activity->user->name }}
        </a>
    @endif
    <a class="flex rounded-lg bg-gradient-to-bl from-gray-900 to-black" href="{{ route('activities.show', $activity) }}">
        <div>
            <img class="h-40 rounded-l-lg" src="{{ route('activities.lofimap', $activity) }}" alt="">
        </div>
        <div class="px-8 pb-0 py-5 flex-1 flex flex-col">
            <div class="flex-1">
                <h2 class="leading-none text-2xl font-bold tracking-wide text-white">{{ $activity->name }}</h2>
                @if ($activity->description)
                    <div class="mt-2 text-xs text-blue-100">{{ \Illuminate\Support\Str::words($activity->description, 10) }}</div>
                @endif
                <div class="mt-2 text-sm font-medium text-blue-200">
                    @datetime($activity->performed_at_tz)
                </div>

            </div>
            <div class="flex space-x-4 text-white">
                <div class="flex flex-col justify-center items-center">
                    <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Moving Time') }}</span>
                    <span class="text-sm font-medium">{{ gmdate('H:i:s', $activity->active_duration) }}</span>
                </div>
                <div class="flex flex-col justify-center items-center">
                    <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Distance') }}</span>
                    <span class="text-sm font-medium">{{ $activity->distance }} km</span>
                </div>
                <div class="flex flex-col justify-center items-center">
                    <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Avg. Speed') }}</span>
                    <span class="text-sm font-medium">{{ round($activity->avg_speed, 1) }} km/h</span>
                </div>
                @if ($activity->ascent)
                <div class="flex flex-col justify-center items-center">
                    <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Ele. Gain') }}</span>
                    <span class="text-sm font-medium">{{ $activity->ascent }} M</span>
                </div>
                @endif
            </div>
        </div>
        <div class="py-4 px-8 text-white flex flex-col">
            <div class="flex-1">
                @if (array_sum($activity->getSegmentAchievements()))
                <div class="text-xs font-bold uppercase">{{ __('Achievements') }}</div>
                <div class="text-xs">
                    @foreach ($activity->getSegmentAchievements() as $level => $count)
                        @if ($count)
                            @switch($level)
                                @case(0)
                                    <span class="inline-block p-1 rounded-lg shadow-sm font-semibold bg-yellow-200 text-yellow-900">🥇×{{ $count }}</span>
                                    @break
                                @case(1)
                                    <span class="inline-block p-1 rounded-lg shadow-sm font-semibold bg-gray-200 text-gray-900">🥈×{{ $count }}</span>
                                    @break
                                @case(2)
                                    <span class="inline-block p-1 rounded-lg shadow-sm font-semibold bg-orange-700 text-white">🥉×{{ $count }}</span>
                                    @break
                            @endswitch
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
            <div class="flex space-x-4">
                {{-- @livewire('activities.likers', ['activity' => $activity, 'inline' => true], 'likers-' . $activity->id) --}}
                <div>
                    <button class="text-center hover:text-blue-700 {{ Auth::user()->hasLiked($activity) ? 'text-blue-700' : '' }}">
                        <svg class="h-6 w-6 mb-1 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                        </svg>
                        {{ $activity->likersCount }}
                    </button>
                </div>
                <div>
                    <svg class="h-6 w-6 mb-1 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    {{ $activity->comments->count() }}
                </div>
            </div>
        </div>
    </a>
</div>