<?php

use CubeAgency\FilamentTreeView\Resources\Pages\CreateTreeViewRecord;

function makeCreateRecord(): CreateTreeViewRecord
{
    return new class extends CreateTreeViewRecord {
        public function __construct() {}

        public function getQueryString(): array
        {
            return $this->queryString;
        }
    };
}

it('parentId defaults to null', function () {
    expect(makeCreateRecord()->parentId)->toBeNull();
});

it('queryString includes parentId', function () {
    expect(makeCreateRecord()->getQueryString())->toContain('parentId');
});
