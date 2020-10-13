<x-jet-form-section submit="update">
    <x-slot name="title">
        {{ __('Wahoo Fitness/Dropbox') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Download your activities from your Wahoo device, via Dropbox.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6">
            @if ($hasToken)
                @isset($dropboxUser['email'])
                <x-status-message>
                    {{ __('Successfully syncing with the Dropbox account :email.', ['email' => $dropboxUser['email']]) }}
                </x-status-message>
                @else
                    <x-status-message type="error" title="{{ __('Oh no') }}">
                        {{ __('There was a problem connecting to your Dropbox account. You may need to disable syncing and try again.') }}
                    </x-status-message>
                @endisset
            @else
                <x-status-message type="warning" title="{{__('Wait, Dropbox?')}}">
                    {{ __('Without building an app, getting access to your rides from Wahoo head units is nearly impossible. The easiest way around this is to configure your Wahoo ELEMENT Companion or Wahoo Fitness app to upload your activities to Dropbox. Once they\'re on Dropbox, we will download and process them.') }}
                </x-status-message>
            @endif
        </div>

        <x-jet-confirmation-modal wire:model="confirmingDisable">
            <x-slot name="title">
                {{ __('Disable Syncing') }}
            </x-slot>

            <x-slot name="content">
                {{-- {{ __('You will need to re-enter your email and password if you with to start syncing with Garmin Connect again.') }} --}}
            </x-slot>

            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$toggle('confirmingDisable')" wire:loading.attr="disabled">
                    {{ __('Nevermind') }}
                </x-jet-secondary-button>

                <x-jet-danger-button class="ml-2" wire:click="disable" wire:loading.attr="disabled">
                    {{ __('Disable Syncing') }}
                </x-jet-button>
            </x-slot>
        </x-jet-confirmation-modal>
    </x-slot>

    <x-slot name="actions">
        @if ($hasToken)
            <x-jet-action-message class="mr-3" on="disabled">
                {{ __('Disabled.') }}
            </x-jet-action-message>

            <x-jet-secondary-button wire:click="confirmDisable" wire:loading.attr="disabled">
                {{ __('Disable Syncing') }}
            </x-jet-secondary-button>
        @else
            <x-jet-button class="w-64 justify-center" onclick="window.location = '{{ route('user.integrations.dropbox') }}'">
                {{ __('Connect to Dropbox') }}
            </x-jet-button>
        @endif
    </x-slot>
</x-jet-form-section>
