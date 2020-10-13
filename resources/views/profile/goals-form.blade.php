<div class="pt-6">
    <div class="flex justify-end">
        <button wire:click="toggle" class="  text-blue-700 font-semibold">{{ __('Manage your goals') }}</button>
    </div>
    <div
        x-data="{ open: @entangle('visible') }"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 h-0"
        x-transition:enter-end="transform opacity-100 h-full"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 h-full"
        x-transition:leave-end="transform opacity-0 h-0"
    >
        @if ($errors->any())
            <x-status-message class="mt-3" type="error">{{ $errors->first() }}</x-status-message>
        @endif
        <div class="flex flex-col divide-y">
            <div class="flex text-sm py-2 font-mono">
                <div class="w-2/12"></div>
                <div class="w-3/12">Type</div>
                <div class="w-3/12">Interval</div>
                <div class="w-3/12">Goal</div>
                <div class="w-1/12"></div>
            </div>
            @foreach ($state as $i => $row)
                <div class="flex items-center text-sm py-2 px-1 h-12">
                    <div class="w-2/12 font-mono">Goal #{{ $i+1 }}</div>
                    <div class="w-3/12">
                        @if (!$user->goals->fresh()->get($i))
                            <select wire:model="state.{{ $i }}.type">
                                <option value="">--</option>
                                <option value="distance">{{ __('Distance') }}</option>
                                <option value="ascent">{{ __('Ascent') }}</option>
                                <option value="duration">{{ __('Duration') }}</option>
                            </select>
                        @else
                            {{ ucfirst($state[$i]['type']) }}
                        @endif
                    </div>
                    <div class="w-3/12">
                        @if (!$user->goals->fresh()->get($i))
                        <select wire:model="state.{{ $i }}.interval">
                            <option value="">--</option>
                            <option value="weekly">{{ __('Weekly') }}</option>
                            <option value="monthly">{{ __('Monthly') }}</option>
                        </select>
                        @else
                            {{ ucfirst($state[$i]['interval']) }}
                        @endif
                    </div>
                    <div class="w-3/12">
                        @if ($state[$i]['type'] === 'distance')
                            <x-jet-input class="text-xs" wire:model.lazy="state.{{ $i }}.goal" />
                            km
                        @elseif ($state[$i]['type'] === 'ascent')
                            <x-jet-input class="text-xs" wire:model.lazy="state.{{ $i }}.goal" />
                            m
                        @elseif ($state[$i]['type'] === 'duration')
                            <x-jet-input class="text-xs w-12" wire:model.lazy="durations.{{ $i }}.h" />
                            h
                            <x-jet-input class="text-xs w-12" wire:model.lazy="durations.{{ $i }}.m" />
                            m
                        @endif
                    </div>
                    <div class="w-1/12 text-right">
                        @if ($user->goals->fresh()->get($i))
                            <x-jet-secondary-button wire:click="confirmDelete({{ $i }})">Delete</x-jet-danger-button>
                        @endif
                    </div>
                </div>
            @endforeach
            <div class="flex items-center justify-end pt-3">
                <x-jet-action-message class="mr-3" on="goals:saved">
                    {{ __('Saved.') }}
                </x-jet-action-message>
                <x-jet-button wire:click="save">Save</x-jet-button>
            </div>
        </div>
        <x-jet-confirmation-modal wire:model="confirmingDelete">
            <x-slot name="title">
                {{ __('Delete goal?') }}
            </x-slot>

            <x-slot name="content">
                {{ __('') }}
            </x-slot>

            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$toggle('confirmingDelete')" wire:loading.attr="disabled">
                    {{ __('Nevermind') }}
                </x-jet-secondary-button>

                <x-jet-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">
                    {{ __('Delete goal') }}
                </x-jet-button>
            </x-slot>
        </x-jet-confirmation-modal>
    </div>
</div>
