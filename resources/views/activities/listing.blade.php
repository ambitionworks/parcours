<div>
    @if ($total)
    <div x-data="{ open: false }" class="text-blue-100 mb-4">
        <div class="space-x-3">
            <x-jet-input placeholder="{{ __('Search by activity name ...') }}" class="h-8 w-64 text-gray-900 text-sm" wire:model.debounce.300ms="name" />
            <button @click="open = !open" class="font-medium" x-text="open ? '{{ __('Close advanced search') }}' : '{{ __('Open advanced search') }}'"></button>
        </div>
        <div class="mt-4 flex bg-white text-gray-900 rounded-lg p-5" x-show="open">
            <div class="w-1/3">
                <x-jet-label class="mb-1">
                    <span class="font-medium text-base">{{ __('Min. distance') }}</span>
                </x-jet-label>
                <x-jet-input class="w-4/5 text-gray-900 text-sm" wire:model.debounce.300ms="min_distance" />
                @error('min_distance')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
            </div>
            <div class="w-1/3">
                <x-jet-label class="mb-1">
                    <span class="font-medium text-base">{{ __('Date between') }}</span>
                </x-jet-label>
                <x-date-picker wire:model="before_date" wrapper="w-4/5" placeholder="{{ __('Performed before ...') }}" />
                <x-date-picker wire:model="after_date" wrapper="w-4/5 mt-4" placeholder="{{ __('Performed after ...') }}" />
            </div>
            <div class="w-1/3">
                <x-jet-label class="mb-1">
                    <span class="font-medium text-base">{{ __('Includes segment') }}</span>
                </x-jet-label>
                @if ($segments)
                    <select class="h-10 form-input shadow-sm rounded-md w-4/5 text-gray-800 text-sm" wire:model="segment">
                        <option value="">{{ __('Select ...') }}</option>
                        @foreach ($user->likes(\App\Models\Segment::class)->get()->pluck('name', 'id') as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                @endif
                <div class="text-xs">
                    <svg class="h-4 w-4 inline-flex" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    {{ __('You will need to ★ a segment before they appear here.') }}
                </div>
            </div>
        </div>
    </div>
    @endif
    <x-activities.list :activities="$activities" :weekDividers="true" wire:key="activities">
        @if (!$total)
        <x-slot name="empty">
            <div class="h-64 flex flex-col items-center justify-center">
                <h2 class="text-5xl text-center font-extrabold bg-clip-text text-transparent bg-gradient-to-t from-gray-900 via-blue-600 to-blue-700">{{ __('No rides yet.') }}</h2>
                @if (!$user->garmin_connect_profile)
                    <x-jet-secondary-button onclick="window.location = '{{ route('user.integrations') }}'">
                        {{ __('Import Activities') }}
                    </x-jet-secondary-button>
                @endif
            </div>
        </x-slot>
        @else
        <x-slot name="empty">
            <div class="h-32 flex flex-col items-center justify-center">
                <h2 class="text-2xl text-center font-medium text-blue-100">{{ __('Sorry, no rides were found...') }}</h2>
            </div>
        </x-slot>
        @endif
    </x-activities.list>
</div>
