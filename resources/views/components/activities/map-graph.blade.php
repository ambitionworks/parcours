@props(['activity', 'start' => null, 'end' => null])

@php
    if ($start && $end) {
        $geoJsonRoute = route('activities.geojson', [$activity, $start, $end]);
        $perfRoute = route('activities.performance', [$activity, $start, $end]);
    } else {
        $geoJsonRoute = route('activities.geojson', $activity);
        $perfRoute = route('activities.performance', $activity);
    }
@endphp

<div
    x-data="mapAndGraph()"
    x-init="mount()"
    @map-segment.window="mapSegment($event.detail.id, $event.detail.start, $event.detail.end)"
    @map-lap.window="mapLap({{ $activity->id }}, $event.detail.id, $event.detail.start, $event.detail.end)"
    {{ $attributes }}
>
    <div class="z-10 rounded-t-lg w-full" id="map" x-ref="map"></div>
    <div class="px-2 flex">
        <div class="w-3/4" id="graph" x-ref="graph"></div>
        <div class="w-1/4 pr-2">
            <div class="p-2 mt-4 bg-gradient-to-bl from-gray-900 via-gray-900 to-black rounded-sm text-gray-200 text-xs font-mono font-semibold">
                <div class="p-1 border-b border-gray-700">{{ __('Performance') }}</div>
                <div class="flex flex-col mt-2 space-y-2">
                    <div class="flex">
                        <span class="w-1/2"></span>
                        <span class="w-1/4">Avg.</span>
                        <span class="w-1/4">Max</span>
                    </div>
                    <div x-show="!perfWindow.power.length && !perfWindow.hr.length && !perfWindow.speed.length && !perfWindow.cadence.length" class="flex p-1 justify-center">
                        {{ __('No performance data') }}
                    </div>
                    <div x-show="perfWindow.power.length" class="flex p-1 hover:bg-gray-800">
                        <span class="w-1/2">{{ __('Power') }}</span>
                        <span class="w-1/4" x-text="perfWindow.power[0]"></span>
                        <span class="w-1/4" x-text="perfWindow.power[1]"></span>
                    </div>
                    <div x-show="perfWindow.hr.length" class="flex p-1 hover:bg-gray-800">
                        <span class="w-1/2"><abbr title="Heart rate">{{ __('HR') }}</abbr></span>
                        <span class="w-1/4" x-text="perfWindow.hr[0]"></span>
                        <span class="w-1/4" x-text="perfWindow.hr[1]"></span>
                    </div>
                    <div x-show="perfWindow.speed.length" class="flex p-1 hover:bg-gray-800">
                        <span class="w-1/2">{{ __('Speed') }}</span>
                        <span class="w-1/4" x-text="perfWindow.speed[0]"></span>
                        <span class="w-1/4" x-text="perfWindow.speed[1]"></span>
                    </div>
                    <div x-show="perfWindow.cadence.length" class="flex p-1 hover:bg-gray-800">
                        <span class="w-1/2">{{ __('Cadence') }}</span>
                        <span class="w-1/4" x-text="perfWindow.cadence[0]"></span>
                        <span class="w-1/4" x-text="perfWindow.cadence[1]"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function mapAndGraph() {
        return {
            map: null,
            graph: null,
            graphMinFill: 0,
            graphData: [],
            routeLayer: null,
            focus: null,
            focusLayer: null,
            focusCache: {},
            defaultPerfWindow: {
                power: [],
                hr: [],
                speed: [],
                cadence: []
            },
            perfWindow: {
                power: [],
                hr: [],
                speed: [],
                cadence: []
            },
            firstTimestamp: null,
            lastTimestamp: null,
            reDraws: 0,
            mount() {
                fetch('{{ $geoJsonRoute }}').then(res => res.json()).then(data => {
                    const getGraphSize = () => {
                        const element = document.getElementById('graph')
                        return {
                            width: element.offsetWidth,
                            height: 300,
                        }
                    }
                    const timestamps = data.features[0].properties.coordTimes
                    const graphOpts = {
                        id: "activity",
                        ...getGraphSize(),
                        plugins: [],
                        series: [{
                            show: false,
                            value: (self, rawValue) => new Date(rawValue * 1000).toLocaleTimeString("en-US"),
                        }],
                        axes: [{
                            grid: { show: false },
                        },{
                            grid: { show: false },
                        }],
                        cursor: { y: false },
                    }

                    this.graphData = [timestamps]
                    this.firstTimestamp = data.features[0].properties.coordTimes[0]
                    this.lastTimestamp = data.features[0].properties.coordTimes[data.features[0].properties.coordTimes.length - 1]

                    if (data.features[0].geometry.coordinates.length) {
                        const points = data.features[0].geometry.coordinates
                        const altitudes = data.features[0].properties.altitude.map(v => Object.values(v)[0])
                        let mapLocationHighlight = null
                        this.graphMinFill = Math.min(...altitudes) > 0 ? 0 : Math.min(...altitudes)

                        document.getElementById('map').className += ' h-64'
                        this.map = L.map(this.$refs.map).setView([51.505, -0.09], 13)

                        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={{ config('parcours.osm_key') }}', {
                            attribution: '<a href="https://www.mapbox.com/about/maps/"><code>© Mapbox</code></a>, <a href="http://www.openstreetmap.org/about/"><code>© OpenStreetMap</code></a> | <a href="https://www.mapbox.com/map-feedback/#/-74.5/40/10"><code>Improve this map</code></a>',
                            maxZoom: 18,
                            id: 'mapbox/dark-v10',
                            tileSize: 512,
                            zoomOffset: -1,
                            accessToken: '{{ config('parcours.osm_key') }}'
                        }).addTo(this.map)

                        if (altitudes.length) {
                            this.graphData.push(altitudes)
                            graphOpts.series.push({
                                label: "Altitude",
                                value: (self, rawValue) => rawValue === null ? '-' : rawValue.toFixed(2) + ' m',
                                stroke: "#3388ff",
                                width: 2,
                                fill: "rgba(0, 0, 255, 0.1)",
                                fillTo: this.graphMinFill,
                            })
                        }

                        const mapSync = () => {
                            return {
                                hooks: {
                                    setCursor: (obj) => {
                                        if (mapLocationHighlight !== null) {
                                                this.map.removeLayer(mapLocationHighlight)
                                        }
                                        if (obj.cursor.idx !== null) {
                                            mapLocationHighlight = L.circle([
                                                points[obj.cursor.idx][1],
                                                points[obj.cursor.idx][0]
                                            ], {
                                                radius: 30,
                                                color: '#ddd'
                                            }).addTo(this.map)
                                        }
                                    }
                                }
                            }
                        }

                        const mapZoom = () => {
                            return {
                                hooks: {
                                    drawClear: obj => {
                                        if (obj.scales.x.min !== this.firstTimestamp || obj.scales.x.max !== this.lastTimestamp) {
                                            const startIdx = obj.posToIdx(0)
                                            const endIdx = obj.valToIdx(obj.scales.x.max)

                                            this.map.fitBounds([
                                                [points[startIdx][1], points[startIdx][0]],
                                                [points[endIdx][1], points[endIdx][0]]
                                            ])
                                        } else {
                                            if (this.focusLayer !== null) this.map.removeLayer(this.focusLayer)
                                            if (++this.reDraws > 1) this.map.fitBounds(this.routeLayer.getBounds())
                                        }
                                    }
                                }
                            }
                        }

                        const perfZoom = () => {
                            return {
                                hooks: {
                                    drawClear: obj => {
                                        if (obj.scales.x.min !== this.firstTimestamp || obj.scales.x.max !== this.lastTimestamp) {
                                            const start = Math.round(obj.scales.x.min)
                                            const end = Math.round(obj.scales.x.max)

                                            fetch('{{ route('activities.performance', $activity) }}/' + start + '/' + end).then(res => res.json()).then(data => {
                                                this._updatePerf(data, false)
                                            })

                                        } else {
                                            if (this.reDraws > 1) this.perfWindow = this.defaultPerfWindow
                                        }
                                    }
                                }
                            }
                        }

                        graphOpts.plugins.push(perfZoom())
                        graphOpts.plugins.push(mapSync())
                        graphOpts.plugins.push(mapZoom())

                        this.routeLayer = L.geoJSON(data).addTo(this.map)
                        this.map.fitBounds(this.routeLayer.getBounds())
                    }

                    this.graph = new uPlot(graphOpts, this.graphData, this.$refs.graph)

                    fetch('{{ $perfRoute }}').then(res => res.json()).then(data => {
                        this._updatePerf(data, true)
                    })
                })
            },
            mapSegment(id, start, end) {
                const focusId = 'segment:' + id
                const geoJsonRoute = '/segments/' + id + '/geojson'
                this._focus(focusId, geoJsonRoute, start, end)
            },
            mapLap(activity, id, start, end) {
                const focusId = 'lap:' + id
                const geoJsonRoute = '/activities/' + activity + '/lap-geojson/' + id
                this._focus(focusId, geoJsonRoute, start, end)
            },
            _updatePerf(data, init) {
                init = init || false
                const props = [
                    {
                        property: 'power',
                        label: '{{ __('Power') }}',
                        stroke: 'purple',
                        fill: 'rgba(150,0,255,0.15)',
                        format: (value) => value + ' W',
                    },
                    {
                        property: 'hr',
                        label: '{{ __('Heart rate') }}',
                        stroke: 'red',
                        fill: 'rgba(150,0,0,0.1)',
                        format: (value) => value + ' BPM',
                    },
                    {
                        property: 'speed',
                        label: '{{ __('Speed') }}',
                        stroke: 'green',
                        fill: 'rgba(0,255,0,0.15)',
                        format: (value) => value.toFixed(2) + ' km/h',
                    },
                    {
                        property: 'cadence',
                        label: '{{ __('Cadence') }}',
                        stroke: 'orange',
                        fill: 'rgba(255,200,0,0.15)',
                        format: (value) => value + ' RPM',
                    }
                ]

                for (const iterator of props) {
                    const prop = iterator.property;
                    if (data[prop].filter(x => !!x).length) {
                        let local = [ Math.round(data[prop].filter(x => x !== null).reduce((a,b) => a + b, 0) / data[prop].filter(x => x !== null).length), Math.max(...data[prop]) ]
                        if (prop === 'speed') {
                            local[1] = local[1].toFixed(2)
                        }

                        this.perfWindow[prop] = local

                        if (init) {
                            this.defaultPerfWindow[prop] = local
                            this.graph.addSeries({
                                label: iterator.label,
                                value: (self, rawValue) => rawValue === null ? '-' : iterator.format(rawValue),
                                stroke: iterator.stroke,
                                width: 1.25,
                                fill: iterator.fill,
                                fillTo: this.graphMinFill,
                            }, this.graphData.length)
                            this.graphData.push(data[prop])
                        }
                    }
                }

                if (init) {
                    this.graph.setData(this.graphData)
                }
            },
            _focus(focusId, geoJsonRoute, start, end) {
                this._resetFocusLayer()
                if (focusId === this.focus) {
                    this._resetFocus()
                } else {
                    this._setFocus(focusId, start, end)
                    if (this.map) {
                        if (typeof this.focusCache[focusId] === 'undefined') {
                            fetch(geoJsonRoute).then(res => res.json()).then(data => {
                                this.focusCache[focusId] = data
                                this._plotFocusLayer(data)
                            })
                        } else {
                            this._plotFocusLayer(this.focusCache[focusId])
                        }
                    }
                }
            },
            _plotFocusLayer(route) {
                this.focusLayer = L.geoJSON(route, {
                    "color": "#00ae00",
                    "weight": 5,
                    "opacity": 0.65
                }).addTo(this.map)
                this.map.fitBounds(this.focusLayer.getBounds())
            },
            _resetFocusLayer() {
                if (this.focusLayer !== null) this.map.removeLayer(this.focusLayer)
            },
            _resetFocus() {
                this.focus = null
                this.perfWindow = this.defaultPerfWindow
                this.graph.setScale('x', { min: this.firstTimestamp, max: this.lastTimestamp })
            },
            _setFocus(id, start, end) {
                this.focus = id
                this.graph.setScale('x', { min: start, max: end })
            }
        }
    }
</script>