<div class="bg-white rounded-lg py-4 px-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-8">
        {{ __('Your activity graph') }}
    </h2>
    <canvas id="dashboard" height="300" class="w-full h-64 relative" id="db"></canvas>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0-beta.4/dist/chart.min.js"></script>
    <script>
        const ctx = document.getElementById('dashboard')
        const raw = @json($stats);
        const types = @json($types);
        const data = [[]]
        let map = {}

        for (let i = 0; i < types.length; i++) {
            map[types[i]] = i+1
            data.push([])
        }

        for (const iterator in raw) {
            data[0].push(iterator)
            for (const type in raw[iterator]) {
                data[map[type]].push(raw[iterator][type])
            }
        }

        var myChart = new Chart(ctx, {
            type: 'line',
            options: {
                animation: false,
                legend: {
                    position: 'bottom',
                },
                tooltips: {
                    mode: 'index',
                    callbacks: {
                        title: function (context) {
                            const d = new Date((parseInt(context[0].label) + new Date().getTimezoneOffset() * 60) * 1000)
                            return '{{ __('Week starting ') }}' + d.toLocaleDateString('default', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            })
                        },
                        label: function(context) {
                            let label = context.dataset.label || ''

                            if (label) {
                                label += ': '
                            }


                            if (context.dataset.label === '{{ __('Duration') }}') {
                                const h = Math.floor(context.dataPoint.y / 3600)
                                const m = Math.round((context.dataPoint.y % 3600) / 60)

                                label += h + 'h ' + m + 'm'
                            } else {
                                if (!isNaN(context.dataPoint.y)) {
                                    label += context.dataPoint.y
                                }
                            }

                            switch (context.dataset.label) {
                                case '{{ __('Distance') }}':
                                    label += ' km'
                                    break
                                case '{{ __('Ascent') }}':
                                    label += ' m'
                                    break
                            }

                            return label
                        }
                    }
                },
                scales: {
                    y: {
                        gridLines: {
                            display: false,
                        },
                    },
                    y1: {

                        position: 'right',
                        gridLines: {
                            display: false,
                        },
                    },
                    y2: {
                        position: 'right',
                        gridLines: {
                            display: false,
                        },
                        ticks: {
                            callback: function (value, index, values) {
                                const h = Math.round(value / 3600)
                                const m = Math.round((value % 3600) / 60)

                                return h + 'h ' + m + 'm'
                            }
                        }
                    },
                    x: {
                        gridLines: {
                            display: false,
                        },
                        ticks: {
                            callback: function(value, index, values) {
                                const curr = new Date(data[0][index] * 1000)
                                const prev = new Date(data[0][index - 1] * 1000)
                                return index === 0 || curr.getMonth() !== prev.getMonth()
                                    ? curr.toLocaleString('default', { month: 'long' })
                                    : ''
                            }
                        }
                    }
                },
            },
            data: {
                labels: data[0],
                datasets: [
                    {
                        type: 'line',
                        label: '{{ __('Distance') }}',
                        data: data[2],
                        pointRadius: 0,
                        backgroundColor: 'rgba(0, 200, 0, 0.3)',
                        borderColor: 'rgba(0, 200, 0, 0.7)',
                    },
                    {
                        type: 'line',
                        label: '{{ __('Ascent') }}',
                        data: data[4],
                        pointRadius: 0,
                        backgroundColor: 'rgba(255, 200, 0, 0.3)',
                        borderColor: 'rgba(255, 200, 0, 0.7)',
                        yAxisID: 'y1',
                    },
                    {
                        type: 'bar',
                        label: '{{ __('Duration') }}',
                        data: data[3],
                        pointRadius: 0,
                        backgroundColor: 'rgba(255, 0, 120, 0.7)',
                        yAxisID: 'y2',
                    },
                    {
                        type: 'bar',
                        label: '{{ __('TSS') }}',
                        data: data[1],
                        backgroundColor: 'rgba(0, 20, 187, 0.6)',
                        // borderColor: 'rgba(0, 20, 187, 0.6)',
                        // borderWidth: 3,
                    },
                ]
            }
        })

    </script>
    @endpush
</div>
