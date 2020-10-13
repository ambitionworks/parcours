<div class="w-full">
    <table class="min-w-full mb-3 shadow rounded border-b border-gray-200 divide-y divide-gray-200">
        <thead class="bg-gray-100 text-left text-xs uppercase">
            <th class="w-4/12 px-6 py-3 font-medium tracking-wider leading-4 text-gray-400">
                {{ __('Activity') }}
            </th>
            <th class="w-2/12 px-6 py-3 font-medium tracking-wider leading-4 text-gray-400 text-center">
                {{ __('Duration') }}
            </th>
            </th>
            <th class="w-6/12 px-6 py-3 font-medium tracking-wider leading-4 text-gray-400">
                {{ __('Performance') }}
            </th>
        </thead>
        <tbody class="text-sm text-gray-700">
            @foreach ($efforts as $effort)
            <tr onclick="window.location = '{{ route('activities.show', $effort->activity) }}'" class="cursor-pointer {{ $loop->index % 2 !== 0 ? 'bg-gray-100 hover:bg-gray-200' : 'hover:bg-gray-200' }}">
                    <td class="w-full px-6 py-3 flex flex-col">
                        <div class="font-semibold">
                            {{ $effort->activity->name }}
                        </div>
                        <div class="w-full">
                            @datetime($effort->activity->performed_at_tz)
                        </div>
                    </td>
                    <td class="w-2/12 px-6 py-3 text-center">
                        @if ($records[0]->elapsed === ($effort->end_time - $effort->start_time))
                        <span class="inline-block px-3 py-1 rounded-lg shadow-sm font-semibold bg-yellow-200 text-yellow-900">🥇
                        @elseif ($records[1]->elapsed === ($effort->end_time - $effort->start_time))
                        <span class="inline-block px-3 py-1 rounded-lg shadow-sm font-semibold bg-gray-200 text-gray-900">🥈
                        @elseif ($records[2]->elapsed === ($effort->end_time - $effort->start_time))
                        <span class="inline-block px-3 py-1 rounded-lg shadow-sm font-semibold bg-orange-600 text-white">🥉
                        @else
                        <span>
                        @endif
                        {{ gmdate('H:i:s', ($effort->end_time - $effort->start_time)) }}</span>
                    </td>
                    <td class="w-full px-6 py-3 flex space-x-3">
                        <div class="flex flex-col justify-center items-center">
                            <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700"><abbr title="{{ __('Average heart rate') }}">{{ __('Avg. HR') }}</abbr></span>
                            <span class="text-sm font-medium">{{ round($effort->avg_hr) }} BPM</span>
                        </div>
                        <div class="flex flex-col justify-center items-center">
                            <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Avg. Power') }}</span>
                            <span class="text-sm font-medium">{{ round($effort->avg_power) }} W</span>
                        </div>
                        <div class="flex flex-col justify-center items-center">
                            <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Avg. Speed') }}</span>
                            <span class="text-sm font-medium">{{ round($effort->avg_speed, 1) }} km/h</span>
                        </div>
                        <div class="flex flex-col justify-center items-center">
                            <span class="block py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Avg. Cadence') }}</span>
                            <span class="text-sm font-medium">{{ round($effort->avg_cadence) }} RPM</span>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $efforts->links() }}
</div>
