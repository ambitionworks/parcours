<div>
    <div x-data="{ open: false }" @click="open = !open" @keydown.window.escape="open = false" @click.away="open = false" class="flex flex-col justify-center items-center px-4 relative cursor-pointer hover:text-blue-700">
        <svg viewBox="0 0 20 20" fill="currentColor" class="cog mb-1 w-6 h-6"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path></svg>
        <span>Options</span>
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="origin-top-right absolute right-0 mt-20 mr-6 w-48 rounded-md shadow-lg"
        >
            <div class="rounded-md bg-white shadow-xs">
                <div class="py-1 font-normal" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                    <a href="{{ route('activities.segments.create', $activity) }}" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" role="menuitem">Create Segment</a>
                    @can('update', $activity)
                    <form method="POST" action="{{ route('activities.process', $activity) }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-900" role="menuitem">
                            Re-process Activity
                        </button>
                    </form>
                    <div class="border-t border-gray-100"></div>
                    <a wire:click="confirmDelete" class="block px-4 py-2 text-sm leading-5 text-red-700 hover:bg-gray-100 hover:text-red-600 focus:outline-none focus:bg-gray-100 focus:text-red-600" role="menuitem">
                        Delete Activity
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    <x-jet-confirmation-modal wire:model="confirmingDelete">
        <x-slot name="title">
            <span class="text-gray-700">{{ __('Delete Activity?') }}</span>
        </x-slot>

        <x-slot name="content">
            <p class="text-gray-700">{{ __('If this activity was imported from an integration, it will not be imported again.') }}</p>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingDelete')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">
                {{ __('Delete Activity') }}
            </x-jet-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
