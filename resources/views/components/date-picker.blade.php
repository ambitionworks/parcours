@props(['placeholder' => '', 'wrapper' => ''])

<div class="relative rounded-md shadow-sm {{ $wrapper ? $wrapper : '' }}">
    <div class="absolute inset-y-0 left-0 px-4 flex items-center pointer-events-none bg-gray-100 rounded-l-md border">
      <span class="text-gray-500 sm:text-sm sm:leading-5">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </span>
    </div>
    <input
        x-data
        x-ref="input"
        x-init="new Pikaday({ field: $refs.input, onSelect: function () {
          console.log(this)
          $dispatch('input', this.toString())
        } })"
        type="text"
        class="form-input block w-full pl-16 text-sm"
        placeholder="{{ $placeholder }}"
        {{ $attributes }}
    >
</div>