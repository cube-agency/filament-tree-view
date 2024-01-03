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
}
