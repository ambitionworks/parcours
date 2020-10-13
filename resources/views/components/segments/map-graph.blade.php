@props(['geojson'])

<div class="w-full space-y-4 mb-4" x-data="mapAndGraph()" x-init="mount()">
    <div class="h-64 z-10 rounded-t-lg" id="map" x-ref="map"></div>
    <div class="px-8 w-full" id="graph" x-ref="graph"></div>
    {{ $slot }}
</div>
<script>
    function mapAndGraph() {
        return {
            map: null,
            graph: null,
            routeLayer: null,
            segmentStartTimestamp: null,
            segmentEndTimestamp: null,
            mount() {
                const flagIcon = L.divIcon({
                    className: 'text-blue-100',
                    html: '<svg viewBox="0 0 20 20" fill="currentColor" class="flag w-6 h-6 -mt-4"><path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"></path></svg>'
                })

                this.map = L.map(this.$refs.map).setView([51.505, -0.09], 13)

                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={{ config('parcours.osm_key') }}', {
                    attribution: '<a href="https://www.mapbox.com/about/maps/"><code>© Mapbox</code></a>, <a href="http://www.openstreetmap.org/about/"><code>© OpenStreetMap</code></a> | <a href="https://www.mapbox.com/map-feedback/#/-74.5/40/10"><code>Improve this map</code></a>',
                    maxZoom: 18,
                    id: 'mapbox/dark-v10',
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: '{{ config('parcours.osm_key') }}'
                }).addTo(this.map)

                const geoJson = @json($geojson);
                const distances = geoJson.features[0].properties.distance
                const altitudes = geoJson.features[0].properties.altitude
                const timestamps = geoJson.features[0].properties.coordTimes
                const points = geoJson.features[0].geometry.coordinates

                let mapLocationHighlight = null, mapSegmentStart = null, mapSegmentEnd = null
                let reDraws = 0

                this.routeLayer = L.geoJSON(geoJson).addTo(this.map)
                this.map.fitBounds(this.routeLayer.getBounds())

                const getGraphSize = () => {
                    const element = document.getElementById('graph')
                    return {
                        width: element.offsetWidth - 64,
                        height: 300,
                    }
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

                const mapMarkSegment = () => {
                    return {
                        hooks: {
                            drawClear: obj => {
                                if (mapSegmentStart !== null) {
                                    this.map.removeLayer(mapSegmentStart)
                                    this.segmentStartTimestamp = null
                                }
                                if (mapSegmentEnd !== null) {
                                    this.map.removeLayer(mapSegmentEnd)
                                    this.segmentEndTimestamp = null
                                }

                                const startIdx = obj.posToIdx(0)
                                const endIdx = obj.valToIdx(obj.scales.x.max)

                                if (startIdx !== 0 && endIdx !== points.length) {
                                    this.segmentStartTimestamp = timestamps[startIdx]
                                    this.segmentEndTimestamp = timestamps[endIdx]
                                    mapSegmentStart = L.marker([
                                        points[startIdx][1],
                                        points[startIdx][0]
                                    ], {icon: flagIcon, title: 'Start'}).addTo(this.map)
                                    mapSegmentEnd = L.marker([
                                        points[endIdx][1],
                                        points[endIdx][0]
                                    ], {icon: flagIcon, title: 'Finish'}).addTo(this.map)

                                    this.map.fitBounds([
                                        [points[startIdx][1], points[startIdx][0]],
                                        [points[endIdx][1], points[endIdx][0]]
                                    ])
                                } else {
                                    if (++reDraws > 1) this.map.fitBounds(this.routeLayer.getBounds())
                                }
                            }
                        }
                    }
                }

                const opts = {
                    id: "segment",
                    ...getGraphSize(),
                    plugins: [
                        mapSync(),
                        mapMarkSegment(),
                    ],
                    series: [{
                        label: 'Distance',
                        show: false,
                        value: (self, rawValue) => rawValue === null ? '-' : rawValue.toFixed(2) + ' km',
                    },
                    {
                        spanGaps: false,
                        label: "Altitude",
                        value: (self, rawValue) => rawValue === null ? '-' : rawValue.toFixed(2) + ' m',
                        stroke: "lightgray",
                        width: 2,
                        fill: "rgba(0, 0, 0, 0.05)",
                        fillTo: (self, seriesIdx, dataMin, dataMax) => dataMin > 0 ? 0 : dataMin
                    }],
                    axes: [{
                        grid: { show: false },
                    },{
                        grid: { show: false },
                    }],
                    cursor: { y: false },
                    scales: { x: { time: false } }
                }

                this.graph = new uPlot(opts, [distances, altitudes], this.$refs.graph)
            }
        }
    }
</script>