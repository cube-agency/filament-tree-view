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
                    :compact="$compact"
                />
            @empty
                <div @class([
                    'w-full bg-white rounded-lg px-3 py-2 text-left rtl:text-right',
                    'divide-y divide-gray-200 !border-t-0',
                    'dark:bg-gray-900 dark:divide-gray/10 dark:border-t-gray/10',
                ])>
                    <div class="fi-ta-empty-state">
                        <div class="fi-ta-empty-state-content">
                            <div class="fi-ta-empty-state-icon-bg">
                                {{ \Filament\Support\generate_icon_html('heroicon-o-bookmark', size: \Filament\Support\Enums\IconSize::Large) }}
                            </div>

                            <h2 class="fi-ta-empty-state-heading">
                                {{ __('filament-tables::table.empty.heading', ['model' => $model]) }}
                            </h2>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <x-filament-actions::modals/>
</x-filament-panels::page>
