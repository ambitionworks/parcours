<x-jet-action-section>
    <x-slot name="title">
        {{ __('Follow Requests') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Confirm or deny requests to follow you.') }}
    </x-slot>

    <x-slot name="content">
        <table class="min-w-full shadow rounded border-b border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-100 text-left text-xs uppercase">
                <th class="px-6 py-3 font-medium tracking-wider leading-4 text-gray-400">{{ __('Request from') }}</th>
                <th class="px-6 py-3 font-medium tracking-wider leading-4 text-gray-400">{{ __('Actions') }}</th>
            </thead>
            <tbody>
                @foreach ($user->follow_requests_received as $request)
                    <tr>
                        <td class="w-3/5 px-6 py-3">
                            <a href="{{ route('user.profile', $request->sender) }}">
                                <x-user-profile-photo class="inline-flex" :user="$request->sender" />
                                {{ $request->sender->name }}
                            </a>
                        </td>
                        <td class="w-2/5 px-6 py-3">
                            @if (isset($status[$request->sender->id]))
                                @if ($status[$request->sender->id])
                                    <span class="rounded-full px-3 py-1 bg-green-100 text-green-700 tracking-wide text-sm font-bold">
                                        <svg class="h-4 w-4 inline-flex" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ __('Confirmed') }}
                                    </span>
                                @else
                                    <span class="rounded-full px-3 py-1 bg-red-100 text-red-700 tracking-wide text-sm font-bold">
                                        <svg class="h-4 w-4 inline-flex" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        {{ __('Denied') }}
                                    </span>
                                @endif
                            @else
                                <x-jet-button wire:click="confirm({{ $request->sender->id }})">Confirm</x-jet-button>
                                <x-jet-secondary-button wire:click="deny({{ $request->sender->id }})">Deny</x-jet-button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-slot>

</x-jet-action-section>
