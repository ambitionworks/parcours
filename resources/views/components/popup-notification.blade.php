@props(['link' => null, 'data'])

@if ($link)
    <a class="block" href="{{ $link }}">
@else
    <div class="block">
@endif
    <div class="px-4 py-2 hover:bg-gray-100">
        {{ $slot }}
        <div class="flex items-center">
            @if (!$data['read'])
            <span class="relative inline-flex mr-1 rounded-full h-1 w-1 bg-blue-500"></span>
            @endif
            <span class="text-gray-500">{{ $data['created_at']->diffForHumans() }}</span>
        </div>
    </div>
@if ($link)
    </a>
@else
    </div>
@endif