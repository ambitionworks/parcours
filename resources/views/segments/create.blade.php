<x-app-layout>
    <x-slot name="title">
        {{ __('Create Segment') }}
    </x-slot>

    <x-slot name="header">
        <div class="flex divide-x divide-blue-300 text-white text-sm font-semibold">
            <a class="hover:text-blue-700" href="{{ route('activities.show', $activity) }}">
                <div class="flex flex-col justify-center items-center px-6 cursor">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="arrow-left mb-1 w-6 h-6"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>
                    <span>Back</span>
                </div>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden sm:rounded-lg">
                <x-segments.map-graph :geojson="$geojson">
                    <form class="px-8" action="{{ route('activities.segments.store', $activity) }}" method="POST">
                        @csrf
                        <input x-model="segmentStartTimestamp" name="segmentStartTimestamp" type="hidden">
                        <input x-model="segmentEndTimestamp" name="segmentEndTimestamp" type="hidden">
                        @if ($errors->has('segmentStartTimestamp') || $errors->has('segmentEndTimestamp'))
                        <x-status-message type="error">
                            {{ __("We couldn't determine the route for your new segment. Please make sure you have selected a route by following the instructions below.") }}
                        </x-status-message>
                        @enderror
                        <div class="flex divide-x divide-gray-200">
                            <div class="w-1/2 pr-4 pt-0 space-y-4">
                                <x-jet-label for="name" value="{{ __('Segment name') }}" />
                                <x-jet-input id="name" name="name" type="text" class="mt-1 block w-full" />
                                <x-jet-input-error for="name" class="mt-2" />
                                <x-jet-button>
                                    {{ __('Create Segment') }}
                                </x-jet-button>
                            </div>
                            <div class="w-1/2 p-4 pt-0">Instructions for creating segments here</div>
                        </div>
                    </form>
                </x-segments.map-graph>
            </div>
        </div>
    </div>
</x-app-layout>