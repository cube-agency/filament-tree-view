<?php

use CubeAgency\FilamentTreeView\Resources\Pages\TreeViewRecords;

function makeFeatureRecords(): TreeViewRecords
{
    return new class extends TreeViewRecords {
        public function __construct() {}
    };
}

it('getMaxDepth returns the configured value', function () {
    expect(makeFeatureRecords()->getMaxDepth())->toBe(1);
});

it('getMaxDepth reflects a config override', function () {
    config(['filament-tree-view.max_depth' => 3]);

    expect(makeFeatureRecords()->getMaxDepth())->toBe(3);
});

it('getCompact returns false by default', function () {
    expect(makeFeatureRecords()->getCompact())->toBeFalse();
});

it('getCompact reflects a config override', function () {
    config(['filament-tree-view.compact' => true]);

    expect(makeFeatureRecords()->getCompact())->toBeTrue();
});
