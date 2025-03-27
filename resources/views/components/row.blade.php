@props(['row', 'page'])

@php
    $url = $this->getRowUrl($row);
    $title = $this->getRowTitle($row);
    $prefix = $this->getRowPrefix($row);
    $suffix = $this->getRowSuffix($row);
    $actions = $this->getRowActions($row);
    $childrenCount = $row->children->count();
@endphp

<div x-data="{
        open: false,
        id: '{{ $row->getKey() }}',
        sessionKey: '{{ str($page)->classBaseName() }}_opened_nodes',
        init() {
            let ids = JSON.parse(sessionStorage.getItem(this.sessionKey)) || [];
            this.open = ids.includes(this.id);
        },
        toggleOpen() {
            this.open = ! this.open;

            let ids = JSON.parse(sessionStorage.getItem(this.sessionKey)) || [];

            if (this.open) {
                if (! ids.includes(this.id)) ids.push(this.id);
            } else {
                ids = ids.filter(id => id !== this.id);
            }

            sessionStorage.setItem(this.sessionKey, JSON.stringify(ids));
        },
    }"
    data-id="{{ $row->getKey() }}"
    class="js-sortable-item"
    wire:key="{{ $row->getKey() }}"
    data-sortable-item
>
    <div class="fi-treeview-row flex items-center bg-white mb-2 px-2 py-2 rounded shadow justify-between dark:bg-gray-800">
        <div class="flex w-full">
            <div class="pr-2" data-sortable-handle>
                <x-filament::icon icon="heroicon-o-bars-2" class="w-6 h-6"/>
            </div>

            @if ($childrenCount)
                <div class="fi-treeview-row-toggle flex items-center pr-2" x-on:click="toggleOpen" x-transition>
                    <x-filament::icon x-show="open" icon="heroicon-o-chevron-up" class="w-5 h-5"/>
                    <x-filament::icon x-show="!open" icon="heroicon-o-chevron-right" class="w-5 h-5"/>
                </div>
            @endif

            <div @class(array_merge(['fi-treeview-row-item', 'flex', 'w-full'], $this->getRowClasses($row)))>
                @if ($prefix)
                    <span class="fi-treeview-row-item-prefix pr-2">{{ $prefix }}</span>
                @endif

                <div class="fi-treeview-row-item-title flex justify-between w-full items-center">
                    @if ($url)
                        <a href="{{ $url }}">{{ $title }}</a>
                    @else
                        {{ $title }}
                    @endif

                    @if ($suffix)
                        <span class="text-sm fi-treeview-row-item-suffix">{{ $suffix }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div>
            @if (count($actions))
                <x-filament-actions::group
                        :actions="$actions"
                        label="Actions"
                        icon="heroicon-m-ellipsis-vertical"
                        color="primary"
                        size="lg"
                        dropdown-placement="bottom-start"
                />
            @endif
        </div>
    </div>

    @if ($childrenCount)
        <div class="ml-8 js-sortable-group" x-show="open" x-collapse.duration.200ms>
            @foreach ($row->children->sortBy('_lft') as $child)
                <x-filament-tree-view::row :row="$child" :page="$page"></x-filament-tree-view::row>
            @endforeach
        </div>
    @else
        <div class="ml-8 js-sortable-group"></div>
    @endif
</div>
