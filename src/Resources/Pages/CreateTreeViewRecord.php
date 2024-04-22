<?php

namespace CubeAgency\FilamentTreeView\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTreeViewRecord extends CreateRecord
{
    public $parentId;

    protected array $queryString = ['parentId'];

    protected function handleRecordCreation(array $data): Model
    {
        $parent = $this->getModel()::find($this->parentId);

        return $this->getModel()::create($data, $parent);
    }

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        $row = $this->getModel()::withDepth()->find($this->parentId);

        abort_unless(
            static::getResource()::canCreate() && $row?->depth < config('filament-tree-view.max_depth'),
            403
        );
    }
}
