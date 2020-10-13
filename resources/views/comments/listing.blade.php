<div class="flex flex-col space-y-3">
    @can('create', [\App\Model\Comment::class, $model])
        <x-comment class="border-b border-gray-100 pb-5 mb-2" :user="$this->user">
            <textarea class="appearance-none text-sm w-full bg-gray-100 placeholder-gray-400 focus:outline-none" placeholder="{{ __('Write a comment...') }}" rows="3" wire:model.defer="newComment"></textarea>
            <div class="flex justify-end items-center">
                <x-jet-action-message class="mr-3" on="posted">
                    {{ __('Posted.') }}
                </x-jet-action-message>
                <x-jet-button wire:click="post">
                    {{ __('Post') }}
                </x-jet-button>
            </div>
        </x-comment>
    @endcan

    @foreach ($commentList as $comment)
        <x-comment :user="$comment->user" :comment="$comment" :parent="$model">
            {{ $comment->comment }}
        </x-comment>
    @endforeach

    <x-jet-confirmation-modal wire:model="confirmingDelete">
        <x-slot name="title">
            <span class="text-gray-700">{{ __('Delete Comment?') }}</span>
        </x-slot>

        <x-slot name="content">
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingDelete')" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">
                {{ __('Delete Comment') }}
            </x-jet-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
