@props(['rows' => '3', 'header' => 'Loading ...'])

<table class="min-w-full shadow rounded border-b border-gray-200 divide-y divide-gray-200">
    <thead class="bg-gray-100 text-left text-xs uppercase">
        <th class="px-6 py-3 font-medium tracking-wider leading-4 text-gray-400">{{ $header }}</th>
    </thead>
    <tbody>
        @for ($i = 0; $i < $rows; $i++)
        <tr>
            <td class="px-6 py-3">
                <div class="min-w-full rounded-full {{ $i % 2 === 0 ? 'bg-gray-200' : 'bg-gray-300' }} animate-pulse">&nbsp;</div>
            </td>
        </tr>
        @endfor
    </tbody>
</table>