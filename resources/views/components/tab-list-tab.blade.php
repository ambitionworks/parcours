@props(['for', 'title'])

<div class="px-3 py-2 font-semibold cursor-pointer rounded-lg shadow" :class="{ 'bg-blue-700 text-white': tab === '{{ $for }}', ' bg-blue-100 text-blue-700 hover:bg-blue-500 hover:text-blue-50': tab !== '{{ $for }}' }" @click="select('{{ $for }}', $dispatch)">{{ $title }}</div>