<x-filament::input.wrapper
    inline-prefix
    prefix-icon="heroicon-m-magnifying-glass"
    class="mb-4 w-1/2"
>
    <x-filament::input
        type="search"
        x-on:input.debounce.300ms="$dispatch('search-input', {value: $event.target.value})"
        placeholder="{{ __('Search...') }}"
        autocomplete="off"
    />
</x-filament::input.wrapper>
