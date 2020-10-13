@props(['activities', 'showUser' => false, 'weekDividers' => false])

<div class="overflow-hidden space-y-6" {{ $attributes }}>
    @forelse ($activities as $activity)
        <x-activities.list-item wire:key="{{ $activity->id }}" :showUser="$showUser" :activity="$activity" />
        @if ($weekDividers && !empty($activities[$loop->index + 1]) && $activities[$loop->index + 1]->performed_at_tz->format('W') !== $activity->performed_at_tz->format('W'))
            <div class="border-b border-gray-800 text-gray-800 text-center text-xs font-bold" style="line-height: 0;">
                <span class="bg-black px-3">{{ __('Week') }} {{ $activity->performed_at_tz->format('W') }}, {{ $activity->performed_at_tz->format('Y') }}</span>
            </div>
        @endif
    @empty
        @if (!empty($empty))
            {{ $empty }}
        @else
            <div class="h-32 flex flex-col items-center justify-center">
                <h2 class="text-2xl text-center font-medium text-blue-100">{{ __('Sorry, nothing here...') }}</h2>
            </div>
        @endif
    @endforelse
    @if (count($activities) && method_exists($activities, 'links'))
        {{ $activities->links() }}
    @endif
</div>