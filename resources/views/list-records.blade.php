<x-filament-panels::page>
    <div class="flex flex-col gap-0">
        <x-filament-tree-view::rows :items="$rows" :page="$page"></x-filament-tree-view::rows>
    </div>

    <x-filament-actions::modals/>
</x-filament-panels::page>
