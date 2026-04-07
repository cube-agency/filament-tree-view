@props(['row', 'page', 'compact' => false])

@php
    $rowKey = (string) $row->getKey();
    $url = $this->getRowUrl($row);
    $title = $this->getRowTitle($row);
    $prefix = $this->getRowPrefix($row);
    $suffix = $this->getRowSuffix($row);
    $actionsLoaded = $this->hasLoadedRowActions($row);
    $actions = $actionsLoaded ? $this->getRowActions($row) : [];
    $background = $this->getRowBackground($row);
    $childrenCount = $row->children->count();
@endphp

<div x-data="{
        open: false,
        id: '{{ $rowKey }}',
        sessionKey: '{{ str($page)->classBaseName() }}_opened_nodes',
        actionsOpen: false,
        actionsLoaded: @js($actionsLoaded),
        actionsLoading: false,
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
        loadActions() {
            if (this.actionsLoaded || this.actionsLoading) {
                return;
            }

            this.actionsLoading = true;

            this.$wire.loadRowActions(this.id)
                .then(() => {
                    this.actionsLoaded = true;
                    this.actionsOpen = true;
                })
                .finally(() => {
                    this.actionsLoading = false;
                });
        },
        toggleActions() {
            if (! this.actionsLoaded) {
                this.loadActions();

                return;
            }

            this.actionsOpen = ! this.actionsOpen;
        },
    }"
    data-id="{{ $rowKey }}"
    class="js-sortable-item"
    wire:key="{{ $rowKey }}"
    data-sortable-item
>
    <div class="fi-treeview-row flex items-center bg-white mb-2
        {{$compact ? 'px-0.5 py-0.5' : 'px-2 py-2'}} rounded shadow justify-between dark:bg-gray-800"
        @if ($background) style="background: {{ $background }};" @endif>
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
        <div class="relative" x-on:keydown.escape.window="actionsOpen = false">
            <button
                type="button"
                class="fi-color fi-color-primary fi-text-color-600 hover:fi-text-color-700 dark:fi-text-color-500 dark:hover:fi-text-color-400 fi-icon-btn fi-size-lg fi-ac-icon-btn-group"
                x-on:click="toggleActions"
                x-bind:disabled="actionsLoading"
                x-bind:aria-expanded="actionsOpen"
                x-bind:aria-busy="actionsLoading"
                aria-haspopup="menu"
                aria-label="Actions"
            >
                <x-filament::icon x-show="!actionsLoading" icon="heroicon-m-ellipsis-vertical" class="fi-icon fi-size-lg" />
                <x-filament::loading-indicator x-show="actionsLoading" class="fi-icon fi-size-lg" />
            </button>

            <div
                x-cloak
                x-show="actionsOpen"
                x-transition:enter-start="fi-opacity-0"
                x-transition:leave-end="fi-opacity-0"
                x-on:click.outside="actionsOpen = false"
                class="fi-dropdown-panel absolute right-0 z-20 mt-2 w-max"
                role="menu"
            >
                <div class="fi-dropdown-list">
                    @if ($actionsLoaded && count($actions))
                        @foreach ($actions as $action)
                            {{ $action->grouped() }}
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($childrenCount)
        <div class="ml-8 js-sortable-group" x-show="open" x-collapse.duration.200ms>
            @foreach ($row->children->sortBy('_lft') as $child)
                <x-filament-tree-view::row :row="$child" :page="$page" :compact="$compact"></x-filament-tree-view::row>
            @endforeach
        </div>
    @else
        <div class="ml-8 js-sortable-group"></div>
    @endif
</div>
