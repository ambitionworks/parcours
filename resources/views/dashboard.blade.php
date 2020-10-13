<x-app-layout>
    <x-slot name="title">
        {{ __('Your Dashboard') }}
    </x-slot>
    <x-slot name="header">
        <div class="flex w-full">
            <x-header-title class="flex-1">{{ __('Dashboard') }}</x-header-title>
            <div class="flex items-center text-white font-semibold text-sm">
                @if ($user->activities()->count())
                <div class="flex flex-col justify-center items-center px-4">
                    <span class="py-1 px-3 rounded-full uppercase font-bold text-xs bg-blue-100 text-blue-700">{{ __('Latest activity') }}</span>
                    <a href="{{ route('activities.show', $user->activities()->orderBy('performed_at', 'desc')->first()) }}">{{ $user->activities()->orderBy('performed_at', 'desc')->first()->name }}</a>
                </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="py-4 px-8 bg-white border-b border-gray-200">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ __('Your goal progress') }}
                    </div>
                    <div class="mt-6 text-gray-500 divide-y space-y-6">
                        <div>
                            @forelse ($user->goals as $goal)
                                <x-goal :goal="$goal" />
                            @empty
                            <div class="flex space-x-6">
                                <div class="h-24 w-24 relative" id="goal-placeholder"></div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-600">{{ __('Set a goal and track your progress') }}</h3>
                                    <p>{{ __('Click "Manage your goals" below to set up a goal') }}</p>
                                </div>
                                @push('scripts')
                                <script>
                                    const bar = new ProgressBar.Circle("#goal-placeholder", {
                                        color: '#252f3f',
                                        strokeWidth: 6,
                                        trailWidth: 2,
                                        easing: 'easeInOut',
                                        duration: 800,
                                        text: {
                                            autoStyleContainer: false
                                        },
                                        from: { color: '#bbb', width: 1 },
                                        to: { color: '#aaa', width: 6 },
                                        // Set default step function for all animate calls
                                        step: function(state, circle) {
                                            circle.path.setAttribute('stroke', state.color);
                                            circle.path.setAttribute('stroke-width', state.width);

                                            var value = Math.round(circle.value() * 100);
                                            if (value === 0) {
                                                circle.setText('0%');
                                            } else {
                                                circle.setText(value  + '%');
                                            }

                                        }
                                    });
                                    bar.animate(Math.random().toFixed(2));
                                </script>
                                @endpush
                            </div>
                            @endforelse
                        </div>
                        @livewire('user.goals-form', ['user' => $user])
                    </div>
                </div>
            </div>
            <div>
                <h2 class="mb-3 text-blue-100 text-3xl font-bold">{{ __('Following Feed') }}</h2>
                <x-activities.list :activities="$activities" :showUser="true" />
            </div>
        </div>
    </div>
</x-app-layout>
