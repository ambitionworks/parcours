<x-jet-form-section submit="update">
    <x-slot name="title">
        {{ __('Metrics') }}
    </x-slot>

    <x-slot name="description">
        {{ __('These values are used to perform analysis on your activities. Changing values here will only affect future activities.') }}
    </x-slot>

    <x-slot name="form">

        <!-- Gender -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="gender" value="{{ __('Gender') }}" />
            <select class="form-input w-full shadow-sm rounded-md" name="gender" id="gender" wire:model="state.gender">
                <option value="male">{{ __('Male') }}</option>
                <option value="female">{{ __('Female') }}</option>
            </select>
            <x-jet-input-error for="gender" class="mt-2" />
        </div>

        <!-- FTP -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="ftp" value="{{ __('Functional Threhold Power') }}" />
            <x-jet-input id="ftp" type="text" class="mt-1 block w-full" wire:model.defer="state.ftp" />
            <x-jet-input-error for="ftp" class="mt-2" />
        </div>

        <!-- Resting HR -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="hr_resting" value="{{ __('Resting Heart Rate') }}" />
            <x-jet-input id="hr_resting" type="text" class="mt-1 block w-full" wire:model.defer="state.hr_resting" />
            <x-jet-input-error for="hr_resting" class="mt-2" />
        </div>

        <!-- Max HR -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="hr_max" value="{{ __('Maximum Heart Rate') }}" />
            <x-jet-input id="hr_max" type="text" class="mt-1 block w-full" wire:model.defer="state.hr_max" />
            <x-jet-input-error for="hr_max" class="mt-2" />
        </div>

        <!-- LT HR -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="hr_lt" value="{{ __('Lactate Threshold Heart Rate') }}" />
            <x-jet-input id="hr_lt" type="text" class="mt-1 block w-full" wire:model.defer="state.hr_lt" />
            <x-jet-input-error for="hr_lt" class="mt-2" />
        </div>

    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-button>
            Save
        </x-jet-button>
    </x-slot>
</x-jet-form-section>
