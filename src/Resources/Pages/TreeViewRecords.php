<?php

namespace CubeAgency\FilamentTreeView\Resources\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class TreeViewRecords extends ListRecords
{
    protected static string $view = 'filament-tree-view::list-records';

    public $page;

    protected bool $hasPermissions = true;

    public function mount(): void
    {
        $this->page = static::$resource;
        $this->hasPermissions = config('filament-tree-view.has_permissions', true);
    }

    protected function getViewData(): array
    {
        return [
            'rows' => $this->getModel()::query()->withDepth()->get()->toTree()->sortBy('_lft'),
            'maxDepth' => $this->getMaxDepth(),
            'sortable' => $this->hasPermissions ? Gate::check('reorder', $this->getModel()) : true,
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

        return array_filter($actions, function ($action) {
            return $action->isVisible();
        });
    }

    public function createChildAction(): Action
    {
        return Action::make('createChild')
            ->visible(function () {
                return $this->hasPermissions ? Gate::check('create', $this->getModel()) : true;
            })
            ->url(function (array $arguments) {
                return static::$resource::getUrl('create') . '?parentId=' . $arguments['row'];
            });
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->visible(function (array $arguments) {
                return $this->hasPermissions ? Gate::check('update', [$this->getModel(), $arguments['row']]) : true;
            })
            ->url(function (array $arguments) {
                return static::$resource::getUrl('edit', [$arguments['row']]);
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->visible(function (array $arguments) {
                return $this->hasPermissions ? Gate::check('delete', [$this->getModel(), $arguments['row']]) : true;
            })
            ->requiresConfirmation()
            ->color('danger')
            ->modalIcon('heroicon-o-trash')
            ->action(function (array $arguments) {
                $row = $this->getModel()::find($arguments['row']);

                $row?->delete();

                $this->redirect(static::$resource::getUrl('index'));
            });
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
}
