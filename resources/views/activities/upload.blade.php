<label
    x-data="activityUpload()"
    x-on:livewire-upload-finish="finished"
    class="inline-block align-middle p-2 rounded-full cursor-pointer text-gray-50 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-200">
    <svg viewBox="0 0 20 20" fill="currentColor" class="upload w-5 h-5"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    <input wire:model="file" type="file" class="hidden" />
    @error('file')
        <span class="absolute mt-2 w-24 p-2 text-xs bg-black text-white">
            {{ $message }}
        </span>
    @enderror
</label>
<script>
    function activityUpload() {
        return {
            finished: function () {
                @this.call('save')
            }
        }
    }
</script>