@props(['for'])

<div x-show="tab === '{{ $for }}'" {{ $attributes }}>
    {{ $slot }}
</div>