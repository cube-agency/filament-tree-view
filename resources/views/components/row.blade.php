@props(['row', 'page'])

<div x-data="{ open: false }">
    <div class="flex items-center bg-white mb-4 px-4 py-4 rounded shadow justify-between">
        <div class="flex">
            @if ($row->children->count())
                <div class="flex items-center pr-2" x-on:click="open =! open" x-transition>
                    <x-filament::icon x-show="open" icon="heroicon-o-chevron-up" class="w-5 h-5"/>
                    <x-filament::icon x-show="!open" icon="heroicon-o-chevron-right" class="w-5 h-5"/>
                </div>
            @endif

            <div>
                <a href="{{ $page::getUrl('edit', [$row]) }}">{{ $row->name }}</a>
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

    <div>
        @if ($row->children)
            <div x-show="open" x-collapse.duration.200ms>
                <x-filament-tree-view::rows :items="$row->children" :page="$page"></x-filament-tree-view::rows>
            </div>
        @endif
    </div>
</div>
