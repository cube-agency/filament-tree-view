<?php

namespace CubeAgency\FilamentTreeView\Resources\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class TreeViewRecords extends ListRecords
{
    protected static string $view = 'filament-tree-view::list-records';

    public $page;
    public $rows;

    public function mount(): void
    {
        $this->page = static::$resource;
        $this->rows = $this->getModel()::query()->whereNull('parent_id')->get();
    }

    public function getActions(): array
    {
        return [
            Action::make('create')
                ->action(function (): void {
                    $this->redirect(static::$resource::getUrl('create'));
                })
        ];
    }

    public function getRowActions(Model $row)
    {
        return [
            ($this->createChildAction())(['row' => $row->getKey()]),
            ($this->editAction())(['row' => $row->getKey()]),
            ($this->deleteAction())(['row' => $row->getKey()])
        ];
    }

    public function createChildAction(): Action
    {
        return Action::make('createChild')
            ->url(function (array $arguments) {
                return static::$resource::getUrl('create') . '?parentId=' . $arguments['row'];
            });
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->url(function (array $arguments) {
                return static::$resource::getUrl('edit', [$arguments['row']]);
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->color('danger')
            ->modalIcon('heroicon-o-trash')
            ->action(function (array $arguments) {
                $row = $this->getModel()::find($arguments['row']);

                $row?->delete();

                $this->redirect(static::$resource::getUrl('index'));
            });
    }
}
