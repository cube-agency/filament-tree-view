<?php

namespace CubeAgency\FilamentTreeView\Resources\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TreeViewRecords extends ListRecords
{
    protected static string $view = 'filament-tree-view::list-records';

    public $page;

    protected bool $hasPermissions = true;

    protected bool $hasUserOnlyPolicy = false;

    protected array $permissionsCache = [];

    public function mount(): void
    {
        $this->page = static::$resource;
        $this->hasPermissions = config('filament-tree-view.has_permissions', true);
        $this->hasUserOnlyPolicy = config('filament-tree-view.has_user_only_policy', false);
    }

    protected function getTreeQueryBuilder(): Builder
    {
        return $this->getModel()::query();
    }

    protected function getViewData(): array
    {
        return [
            'rows' => $this->getTreeQueryBuilder()->withDepth()->get()->toTree()->sortBy('_lft'),
            'maxDepth' => $this->getMaxDepth(),
            'sortable' => $this->canReorder(),
            'compact' => $this->getCompact(),
            'model' => static::getResource()::getPluralModelLabel(),
        ];
    }

    public function sortRows($data): void
    {
        $this->getModel()::rebuildTree($data);
    }

    public function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getRowActions(Model $row): array
    {
        $actions = [
            ($this->editAction())(['row' => $row]),
            ($this->deleteAction())(['row' => $row]),
        ];

        if ($row->depth < $this->getMaxDepth()) {
            array_unshift($actions, ($this->createChildAction())(['row' => $row]));
        }

        return $actions;
    }

    public function getRowUrl(Model $row): ?string
    {
        return $this->page::getUrl('edit', [$row]);
    }

    public function canReorder(): bool
    {
        if (!$this->hasPermissions) {
            return true;
        }

        return $this->permissionsCache['canReorder'] = $this->permissionsCache['canReorder']
            ?? static::getResource()::canReorder();
    }

    public function createChildAction(): Action
    {
        return Action::make('createChild')
            ->authorize(fn () => $this->canCreate())
            ->url(function (array $arguments) {
                return static::$resource::getUrl('create') . '?parentId=' . $arguments['row']['id'];
            });
    }

    public function canCreate(): bool
    {
        if (!$this->hasPermissions) {
            return true;
        }

        return $this->permissionsCache['canCreate'] = $this->permissionsCache['canCreate']
            ?? static::getResource()::canCreate();
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->authorize(fn (array $arguments) => $this->canEdit($arguments['row']))
            ->url(function (array $arguments) {
                return static::$resource::getUrl('edit', [$arguments['row']]);
            });
    }

    public function canEdit(Model $row): bool
    {
        if (!$this->hasPermissions) {
            return true;
        }

        if (!$this->hasUserOnlyPolicy) {
            return static::getResource()::canEdit($row);
        }

        return $this->permissionsCache['canEdit'] = $this->permissionsCache['canEdit']
            ?? static::getResource()::canEdit($row);
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->authorize(function (array $arguments) {
                $model = app(static::getModel());
                $row = $model->newInstance($arguments['row'])
                    ->forceFill(['id' => $arguments['row']['id']]);

                return $this->canDelete($row);
            })
            ->requiresConfirmation()
            ->color('danger')
            ->modalIcon('heroicon-o-trash')
            ->action(function (array $arguments) {
                $row = $this->getModel()::find($arguments['row']['id']);

                $row?->delete();
            })
            ->successRedirectUrl(fn () => static::$resource::getUrl('index'))
            ->failureRedirectUrl(fn () => static::$resource::getUrl('index'));
    }

    public function canDelete(Model $row): bool
    {
        if (!$this->hasPermissions) {
            return true;
        }

        if (!$this->hasUserOnlyPolicy) {
            return static::getResource()::canDelete($row);
        }

        return $this->permissionsCache['canDelete'] = $this->permissionsCache['canDelete']
            ?? static::getResource()::canDelete($row);
    }

    public function getRowTitle(Model $row): ?string
    {
        return $row->getAttribute('name');
    }

    public function getRowClasses(Model $row): array
    {
        return [];
    }

    public function getRowPrefix(Model $row): ?string
    {
        return '';
    }

    public function getRowSuffix(Model $row): ?string
    {
        return '';
    }

    public function getMaxDepth(): int
    {
        return config('filament-tree-view.max_depth');
    }

    public function getCompact(): bool
    {
        return config('filament-tree-view.compact', false);
    }
}
