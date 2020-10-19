@props(['goal'])

@php
    $progress = $goal->progress;
    $unit = '';
    $type = ucfirst($goal->type);
    $interval = ucfirst($goal->interval);
    switch ($goal->type) {
        case 'distance':
            $unit = 'km';
            break;
        case 'ascent':
            $unit = 'm';
            break;
    }
@endphp

@if ($progress !== false)
    <div class="inline-flex flex-col w-1/5 items-center">
        <canvas class="absolute" id="goal-{{ $goal->id }}-container"></canvas>
        <div class="text-gray-700 font-semibold mb-2">{{ $type }} ({{ $interval }})</div>
        <div class="h-16 w-16 relative text-gray-800" id="goal-{{ $goal->id }}"></div>
        <div class="mt-2 text-xs">
            @if ($goal->type === 'duration')
                {{ floor($progress / 3600) }} h
                {{ round(($progress % 3600) / 60) }} m
                {{ __('of') }}
                {{ $goal->goal / 3600 }} h
                {{ ($goal->goal % 3600) / 60 }} m
            @else
                {{ $progress }}{{ $unit ? ' '.$unit : ''}} {{ __('of') }} {{ $goal->goal }}{{ $unit ? ' '.$unit : ''}}
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        (() => {
            const progress = {{ $progress }};
            const goal = {{ $goal->goal }};
            if (progress > goal) {
                setTimeout(() => {
                    let canvas = document.getElementById('goal-{{ $goal->id }}-container');
                    canvas.confetti = canvas.confetti || confetti.create(canvas, { resize: true });
                    canvas.confetti({
                        particleCount: 100,
                        spread: 30,
                        origin: { y: 1.2 }
                    });
                }, 750)
            }
            const bar = new ProgressBar.Circle("#goal-{{ $goal->id }}", {
                color: '#252f3f',
                strokeWidth: 6,
                trailWidth: 2,
                easing: 'easeInOut',
                duration: 800,
                text: {
                    autoStyleContainer: false
                },
                from: { color: '#faa', width: 1 },
                to: { color: '#3ea', width: 6 },
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
            bar.text.style.fontSize = '1rem';
            bar.animate(progress >= goal ? 1.0 : (progress / goal));
        })()
    </script>
    @endpush
@endif