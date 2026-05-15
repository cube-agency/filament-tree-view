<?php

use CubeAgency\FilamentTreeView\Resources\Pages\TreeViewRecords;
use Illuminate\Database\Eloquent\Model;

function makeRecords(bool $hasPermissions = false, bool $hasUserOnlyPolicy = false): TreeViewRecords
{
    return new class($hasPermissions, $hasUserOnlyPolicy) extends TreeViewRecords {
        public function __construct(bool $permissions = false, bool $userOnly = false)
        {
            $this->hasPermissions = $permissions;
            $this->hasUserOnlyPolicy = $userOnly;
        }
    };
}

function makeModel(array $attributes = []): Model
{
    $model = new class extends Model {
        protected $guarded = [];
    };

    return $model->forceFill($attributes);
}

// Row rendering

it('getRowTitle returns the name attribute', function () {
    $page = makeRecords();
    $model = makeModel(['name' => 'Root Category']);

    expect($page->getRowTitle($model))->toBe('Root Category');
});

it('getRowTitle returns null when name is absent', function () {
    $page = makeRecords();
    $model = makeModel();

    expect($page->getRowTitle($model))->toBeNull();
});

it('getRowClasses returns an empty array', function () {
    expect(makeRecords()->getRowClasses(makeModel()))->toBe([]);
});

it('getRowPrefix returns an empty string', function () {
    expect(makeRecords()->getRowPrefix(makeModel()))->toBe('');
});

it('getRowSuffix returns an empty string', function () {
    expect(makeRecords()->getRowSuffix(makeModel()))->toBe('');
});

it('getRowBackground returns null', function () {
    expect(makeRecords()->getRowBackground(makeModel()))->toBeNull();
});

// Permissions disabled

it('canReorder returns true when permissions are disabled', function () {
    expect(makeRecords(hasPermissions: false)->canReorder())->toBeTrue();
});

it('canCreate returns true when permissions are disabled', function () {
    expect(makeRecords(hasPermissions: false)->canCreate())->toBeTrue();
});

it('canEdit returns true when permissions are disabled', function () {
    expect(makeRecords(hasPermissions: false)->canEdit(makeModel()))->toBeTrue();
});

it('canDelete returns true when permissions are disabled', function () {
    expect(makeRecords(hasPermissions: false)->canDelete(makeModel()))->toBeTrue();
});

// Row action loading

it('hasLoadedRowActions returns false before loading', function () {
    $page = makeRecords();
    $model = makeModel(['id' => 1]);

    expect($page->hasLoadedRowActions($model))->toBeFalse();
});

it('hasLoadedRowActions returns true after loading', function () {
    $page = makeRecords();
    $model = makeModel(['id' => 5]);

    $page->loadRowActions(5);

    expect($page->hasLoadedRowActions($model))->toBeTrue();
});

it('loadRowActions coerces integer keys to strings', function () {
    $page = makeRecords();
    $model = makeModel(['id' => 99]);

    $page->loadRowActions(99);

    expect($page->loadedRowActions)->toHaveKey('99')
        ->and($page->hasLoadedRowActions($model))->toBeTrue();
});
