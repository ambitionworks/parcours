<x-jet-form-section submit="update">
    <x-slot name="title">
        {{ __('Garmin Connect') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Allow your activities to be synced with Garmin Connect.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6">
            @if (!$user->garmin_connect_profile)
                <x-status-message type="warning" title="{{__('Why do you need this information?')}}">
                    {{ __('Unless you are a company willing to pay thousands of dollars, Garmin does not currently provide a way to access your own data via an API. Instead, we\'re forced to collect your Garmin Connect email and password so that we may automatically connect and download your activities. Sorry for the inconvenience.') }}
                </x-status-message>
            @else
                @if ($user->garmin_connect_profile->api()->getUser())
                    <x-status-message>
                        {{ __('Successfully syncing with the Garmin Connect account :name.', ['name' => $user->garmin_connect_profile->api()->getUser()->displayName]) }}
                    </x-status-message>
                @else
                    <x-status-message type="error" title="{{ __('Oh no') }}">
                        <p>{{ __('There was a problem communicating with Garmin Connect. Please ensure your credentials are correct.') }}</p>
                        @if ($user->garmin_connect_profile->getApiException())
                        <p><em><strong>{{ __('Garmin responded') }}:</strong> {{ $user->garmin_connect_profile->getApiException() }}</em></p>
                        @endif
                    </x-status-message>
                @endif
            @endif
        </div>
        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="email" value="Email" />
            <x-jet-input id="email" type="email" class="mt-1 block w-full" wire:model.defer="state.email" />
            <x-jet-input-error for="email" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="password" value="Password" />
            <x-jet-input id="password" type="password" class="mt-1 block w-full" wire:model.defer="state.password"/>
            <x-jet-input-error for="password" class="mt-2" />
        </div>
        <x-jet-confirmation-modal wire:model="confirmingDisable">
            <x-slot name="title">
                {{ __('Disable Syncing') }}
            </x-slot>

            <x-slot name="content">
                {{ __('You will need to re-enter your email and password if you with to start syncing with Garmin Connect again.') }}
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
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-action-message class="mr-3" on="disabled">
            {{ __('Disabled.') }}
        </x-jet-action-message>

        @if ($user->garmin_connect_profile)
            <x-jet-secondary-button wire:click="confirmDisable" wire:loading.attr="disabled" class="mr-6">
                {{ __('Disable Syncing') }}
            </x-jet-secondary-button>
        @endif

        <x-jet-button>
            Save
        </x-jet-button>
    </x-slot>
</x-jet-form-section>
