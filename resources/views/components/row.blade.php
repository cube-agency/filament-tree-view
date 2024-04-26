@props(['row', 'page'])

<div x-data="{ open: false }"
     data-id="{{ $row->getKey() }}"
     class="js-sortable-item"
     wire:key="{{ $row->getKey() }}"
     data-sortable-item
>
    <div class="flex items-center bg-white mb-2 px-2 py-2 rounded shadow justify-between">
        <div class="flex w-full">
            <div class="js-sortable-handle pr-2">
                <x-filament::icon icon="heroicon-o-bars-2" class="w-6 h-6"/>
            </div>

            @if ($row->children->count())
                <div class="flex items-center pr-2" x-on:click="open =! open" x-transition>
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
            <x-filament-actions::group
                :actions="$this->getRowActions($row)"
                label="Actions"
                icon="heroicon-m-ellipsis-vertical"
                color="primary"
                size="lg"
                dropdown-placement="bottom-start"
            />
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
