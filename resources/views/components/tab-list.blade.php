@props(['default' => null])

<div {{ $attributes }} x-data="tabList()">
    <div class="flex space-x-4 pb-4 border-b border-gray-200">{{ $tabs }}</div>
    <div class="mt-4">{{ $contents }}</div>
</div>
<script>
    function tabList() {
        return {
            tab: '{{ $default ? "$default" : 'null' }}',
            select(tab, dispatcher) {
                this.tab = tab
                dispatcher('tab-select-' + tab)
            }
        }
    }
</script>