<div x-data="segmentMap()" x-init="mount">
    <div wire:ignore x-ref="map" id="map" class="rounded-t-lg z-0"></div>
    <div class="my-5 px-5 flex flex-col space-y-5">
        <div class="flex justify-left">
            <x-jet-label for="name" value="{{ __('Segment Name') }}" class="w-2/12 flex items-center font-bold" />
            <x-jet-input id="name" wire:model="name" class="w-8/12" />
        </div>
        <div class="flex justify-left">
            <x-jet-label for="distance" value="{{ __('Min. segment distance') }}" class="w-2/12 flex items-center font-bold" />
            <x-jet-input id="distance" wire:model="distance" class="w-8/12" />
        </div>
        <div class="flex-col">
            <h3 class="mb-2 text-lg font-semibold text-gray-900 border-b pb-2">{{ __('Results') }}</h3>
            @if ($lat)
                <table x-data class="min-w-full mb-2 shadow rounded border-b border-gray-200 divide-y divide-gray-200">
                    <thead class="bg-gray-100 text-left text-xs uppercase">
                        <th class="max-w-1/12"></th>
                        <th class="w-6/12 py-3 font-medium tracking-wider leading-4 text-gray-400">
                            {{ __('Segment Name') }}
                        </th>
                        <th class="w-3/12 px-6 py-3 font-medium tracking-wider leading-4 text-gray-400 text-center">
                            {{ __('Personal Best') }}
                        </th>
                        </th>
                        <th class="w-2/12 px-6 py-3 font-medium tracking-wider leading-4 text-gray-300 flex justify-center">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
                            </svg>
                        </th>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                    @forelse ($results as $segment)
                    <tr class="{{ $loop->index % 2 !== 0 ? 'bg-gray-100 hover:bg-gray-200' : 'hover:bg-gray-200' }}">
                            <td>
                                @livewire('segments.favourite', ['segment' => $segment])
                            </td>
                            <td class="py-3 flex flex-col space-y-1">
                                <div class="font-medium">{{ $segment->name }}</div>
                                <div class="text-xs text-gray-500 flex space-x-2">
                                    @if ($segment->distance)
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="inline w-4 h-4" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12 1.586l-4 4v12.828l4-4V1.586zM3.707 3.293A1 1 0 002 4v10a1 1 0 00.293.707L6 18.414V5.586L3.707 3.293zM17.707 5.293L14 1.586v12.828l2.293 2.293A1 1 0 0018 16V6a1 1 0 00-.293-.707z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $segment->distance }} km
                                    </span>
                                    @endif
                                    @if ($segment->altitude_change)
                                    <span>
                                        @if ($segment->altitude_change > 0)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="inline w-4 h-4" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                                        </svg>
                                        @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="inline w-4 h-4" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
                                        </svg>
                                        @endif
                                        {{ $segment->altitude_change }} m
                                        @if ($segment->altitude_change && $segment->distance)
                                        ({{ round(($segment->altitude_change / ($segment->distance * 1000)) * 100) }}%)
                                        @endif
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-3 text-center">
                                {{-- {{ print_r($segment->user_efforts->toArray()) }} --}}
                                @if (isset($segment->user_efforts->first()->elapsed))
                                    <span class="px-2 py-1">{{ gmdate('H:i:s', $segment->user_efforts->first()->elapsed) }}</span>
                                @else
                                    {{ __('Not recorded') }}
                                @endif
                            </td>

                            <td class="px-6 py-3 text-center">
                                <a href="{{ route('segments.show', $segment) }}"><x-jet-button>{{ __('View') }}</x-jet-button></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3" colspan="4">{{ __('No results matched your search.') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                {{ $results->links() }}
            @else
                <p class="text-sm">{{ __('Click and drag the map above to draw a search region. Use the outer handle to expand or contract the radius. Use the inner handle to move the region.') }}</p>
            @endif
        </div>
    </div>
    <script>
        function segmentMap() {
            return {
                map: null,
                lat: @entangle('lat'),
                lng: @entangle('lng'),
                radius: @entangle('radius'),
                mount() {
                    let committed = false

                    this.$refs.map.className += ' h-64'

                    window.map = this.map = L.map(this.$refs.map, { editable: true })

                    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={{ config('parcours.osm_key') }}', {
                        attribution: '<a href="https://www.mapbox.com/about/maps/"><code>© Mapbox</code></a>, <a href="http://www.openstreetmap.org/about/"><code>© OpenStreetMap</code></a> | <a href="https://www.mapbox.com/map-feedback/#/-74.5/40/10"><code>Improve this map</code></a>',
                        maxZoom: 18,
                        id: 'mapbox/dark-v10',
                        tileSize: 512,
                        zoomOffset: -1,
                        accessToken: '{{ config('parcours.osm_key') }}'
                    }).addTo(this.map)

                    L.EditControl = L.Control.extend({
                        options: {
                            position: 'topleft',
                            callback: null,
                            kind: '',
                            html: ''
                        },

                        onAdd: function (map) {
                            var container = L.DomUtil.create('div', 'leaflet-control leaflet-bar'),
                                link = L.DomUtil.create('a', '', container);

                            link.href = '#';
                            link.title = 'Create a new ' + this.options.kind;
                            link.innerHTML = this.options.html;
                            L.DomEvent.on(link, 'click', L.DomEvent.stop)
                                    .on(link, 'click', function () {
                                        window.LAYER = this.options.callback.call(map.editTools);
                                    }, this);

                            return container;
                        }

                    });

                    this.map.on('load', () => {
                        L.NewCircleControl = L.EditControl.extend({
                            options: {
                                position: 'topleft',
                                callback: this.map.editTools.startCircle,
                                kind: 'circle',
                                html: '⬤'
                            }
                        });

                        // this.map.addControl(new L.NewCircleControl());
                        this.map.editTools.startCircle()
                        const debounced = debounce((event) => {
                            if (committed) {
                                // console.log('move')
                                this.lat = event.layer.getBounds().getCenter().lat
                                this.lng = event.layer.getBounds().getCenter().lng
                                this.radius = event.layer.getRadius()
                            }
                        }, 300)
                        this.map.on('editable:drawing:move', debounced)

                        this.map.on('editable:drawing:commit', (event) => {
                            this.lat = event.layer.getBounds().getCenter().lat
                            this.lng = event.layer.getBounds().getCenter().lng
                            this.radius = event.layer.getRadius()
                            committed = true
                        })
                    })

                    this.map.locate({ setView: true, maxZoom: 10})
                }
            }
        }
    </script>
</div>
