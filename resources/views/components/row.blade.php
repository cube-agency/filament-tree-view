@props(['row', 'page'])

<div x-data="{
        open: false,
        page: '{{ str($page)->classBaseName() }}',
        id: '{{ $row->getKey() }}',
        getSessionKey() {
            return `${this.page}_opened_nodes`;
        },
        init() {
            let ids = JSON.parse(sessionStorage.getItem(this.getSessionKey())) || [];
            this.open = ids.includes(this.id);
        },
        toggleOpen() {
            this.open = ! this.open;

            const sessionKey = this.getSessionKey();
            let ids = JSON.parse(sessionStorage.getItem(sessionKey)) || [];

            if (this.open) {
                if (! ids.includes(this.id)) ids.push(this.id);
            } else {
                ids = ids.filter(id => id !== this.id);
            }

            sessionStorage.setItem(sessionKey, JSON.stringify(ids));
        },
    }"
    data-id="{{ $row->getKey() }}"
    class="js-sortable-item"
    wire:key="{{ $row->getKey() }}"
    data-sortable-item
>
    <div class="flex items-center bg-white mb-2 px-2 py-2 rounded shadow justify-between dark:bg-gray-800">
        <div class="flex w-full">
            <div class="pr-2" data-sortable-handle>
                <x-filament::icon icon="heroicon-o-bars-2" class="w-6 h-6"/>
            </div>

            @if ($row->children->count())
                <div class="flex items-center pr-2" x-on:click="toggleOpen" x-transition>
                    <x-filament::icon x-show="open" icon="heroicon-o-chevron-up" class="w-5 h-5"/>
                    <x-filament::icon x-show="!open" icon="heroicon-o-chevron-right" class="w-5 h-5"/>
                </div>
            @endif

            <div @class(array_merge(['flex', 'w-full'], $this->getRowClasses($row)))>
                @if ($this->getRowPrefix($row))
                    <span class="pr-2">{{ $this->getRowPrefix($row) }}</span>
                @endif

                <div class="flex justify-between w-full items-center">
                    <a href="{{ $page::getUrl('edit', [$row]) }}">{{ $this->getRowTitle($row) }}</a>
                    @if ($this->getRowSuffix($row))
                        <span class="text-sm">{{ $this->getRowSuffix($row) }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div>
            @if (count($this->getRowActions($row)))
                <x-filament-actions::group
                        :actions="$this->getRowActions($row)"
                        label="Actions"
                        icon="heroicon-m-ellipsis-vertical"
                        color="primary"
                        size="lg"
                        dropdown-placement="bottom-start"
                />
            @endif
        </div>
    </div>

    @if ($row->children->count())
        <div class="ml-8 js-sortable-group" x-show="open" x-collapse.duration.200ms>
            @foreach ($row->children->sortBy('_lft') as $child)
                <x-filament-tree-view::row :row="$child" :page="$page"></x-filament-tree-view::row>
            @endforeach
        </div>
    @endif
</div>
