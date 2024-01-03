@props(['items', 'page'])

@foreach ($items as $row)
    <div class="{{ $row->isChild() ? ' ml-8' : '' }}">
        <x-filament-tree-view::row :row="$row" :page="$page"></x-filament-tree-view::row>
    </div>
@endforeach
