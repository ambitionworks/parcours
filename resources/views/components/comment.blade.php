@props(['user', 'comment' => null, 'parent' => null])

<div {{ $attributes->merge(['class' => 'flex']) }}>
    @if ($comment)
        <x-jet-dropdown align="left" width="48" class="">
            <x-slot name="trigger">
                <button class="flex text-sm rounded-full w-8 focus:outline-none">
                    <x-user-profile-photo :user="$user" />
                </button>
            </x-slot>

            <x-slot name="content">
                <x-jet-dropdown-link href="{{ route('user.profile', $user) }}">
                    {{ __('View Profile') }}
                </x-jet-dropdown-link>
                @can('delete', $comment, $parent)
                    <div class="border-t border-gray-100"></div>
                    <a wire:click="confirmDelete({{$comment->id}})" class="block cursor-auto px-4 py-2 text-sm leading-5 text-red-700 hover:bg-gray-100 hover:text-red-600 focus:outline-none focus:bg-gray-100 focus:text-red-600" role="menuitem">
                        {{ __('Delete Comment') }}
                    </a>
                @endcan
            </x-slot>
        </x-jet-dropdown>
    @else
        <x-user-profile-photo :user="$user" />
    @endif


    <div class="mt-2 ml-2 h-0 w-0 border-gray-100 border-t border-r-8 border-b-8" style="border-bottom-color: transparent;"></div>
    <div class="mt-2 p-2 text-sm bg-gray-100 w-full rounded rounded-tl-none">
        @if ($comment)
            <div class="float-right text-gray-400 text-xs">{{ $comment->user->name }} <span title="{{ $comment->created_at }}">{{ $comment->created_at->diffForHumans() }}</span></div>
        @endif
        {{ $slot }}
    </div>
</div>