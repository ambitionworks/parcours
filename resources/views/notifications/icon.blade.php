<div wire:poll.10s>
    <x-jet-dropdown align="right" width="64">
        <x-slot name="trigger">
            <button wire:click="toggle" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                @if ($hasUnread)
                <span class="flex h-3 w-3 absolute right-0 -mt-2 -mr-1">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                </span>
                @endif
                <svg class="text-white h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="block px-4 py-2 text-xs text-gray-400">
                {{ __('Notifications') }}
            </div>
            <div class="divide-y divide-gray-100 text-xs">
                @forelse ($notifications as $notification)
                    @include($notification['view'], ['data' => $notification['data']])
                @empty
                    <div class="p-3 text-xs">{{ __('No notifications.') }}</div>
                @endforelse
                <div class="flex p-2 text-gray-600 font-medium">
                    @if ($moreUnread)
                        <div>{{ $moreUnread }} {{ __('more unread') }}</div>
                    @endif
                    {{-- <div class="flex-1 text-right">See all</div> --}}
                </div>
            </div>
        </x-slot>
    </x-jet-dropdown>
</div>