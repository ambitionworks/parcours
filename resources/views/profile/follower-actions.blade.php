<div>
    @if ($state['canFollow'])
        <x-jet-button wire:click="follow">{{ __('Follow') }}</x-jet-button>
    @endif
    @if ($state['canCancelRequest'])
        <x-jet-button wire:click="cancel">{{ __('Cancel Follow Request') }}</x-jet-button>
    @endif
    @if ($state['canConfirmRequest'])
        <x-jet-button wire:click="confirm">{{ __('Confirm Follow Request') }}</x-jet-button>
    @endif
    @if ($state['canUnfollow'])
        <x-jet-button wire:click="unfollow">{{ __('Unfollow') }}</x-jet-button>
    @endif
</div>
