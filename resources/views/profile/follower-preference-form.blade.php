<x-jet-form-section submit="updatePreferences">
    <x-slot name="title">
        {{ __('Preferences') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Configure your follower preferences.') }}
    </x-slot>

    <x-slot name="form">
        <div class="flex space-x-4 col-span-6">
            <x-jet-label for="follower_preference[1]" class="flex p-2 w-1/3 border rounded-md shadow-sm">
                <div class="mr-2">
                    <input type="radio" id="follower_preference[1]" wire:model="state.follower_preference" value="1" />
                </div>
                <div class="flex flex-col">
                    <div class="font-semibold">{{ __('Automatically accept') }}</div>
                    <div class="text-xs">{{ __('Any requests to follow will be automatically accepted.') }}</div>
                </div>
            </x-jet-label>
            <x-jet-label for="follower_preference[0]" class="flex p-2 w-1/3 border rounded-md shadow-sm">
                <div class="mr-2">
                    <input type="radio" id="follower_preference[0]" wire:model="state.follower_preference" value="0" />
                </div>
                <div class="flex flex-col">
                    <div class="font-semibold">{{ __('Approval required') }}</div>
                    <div class="text-xs">{{ __('You can approve or deny follower requests.') }}</div>
                </div>
            </x-jet-label>
            <x-jet-label for="follower_preference[-1]" class="flex p-2 w-1/3 border rounded-md shadow-sm">
                <div class="mr-2">
                    <input type="radio" id="follower_preference[-1]" wire:model="state.follower_preference" value="-1" />
                </div>
                <div class="flex flex-col">
                    <div class="font-semibold">{{ __('No requests') }}</div>
                    <div class="text-xs">{{ __('Other users will not be able to follow you.') }}</div>
                </div>
            </x-jet-label>

            <x-jet-input-error for="follower_preference" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            Saved.
        </x-jet-action-message>

        <x-jet-button>
            Save
        </x-jet-button>
    </x-slot>
</x-jet-form-section>
