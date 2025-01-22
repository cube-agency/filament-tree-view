<x-filament-panels::page>
    <div
            class="space-y-2 filament-tree-items"
            x-data="sortableTree({
                maxDepth: @js($maxDepth),
                sortable: @js($sortable)
            })"
    >
        <div id="js-sortable-root-nodes" class="js-sortable-group">
            @forelse($rows as $row)
                <x-filament-tree-view::row
                        :row="$row"
                        :page="$page"
                />
            @empty
                <div @class([
                    'w-full bg-white rounded-lg px-3 py-2 text-left rtl:text-right',
                    'divide-y divide-gray-200 !border-t-0',
                    'dark:bg-gray-900 dark:divide-gray/10 dark:border-t-gray/10',
                ])>
                    <x-filament-tables::empty-state
                        :heading="__('filament-tables::table.empty.heading', ['model' => $model])"
                        icon="heroicon-o-x-mark"
                    />
                </div>
            @endforelse
        </div>
    </div>

    <x-filament-actions::modals/>
</x-filament-panels::page>
