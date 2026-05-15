<?php

use CubeAgency\FilamentTreeView\FilamentTreeViewPlugin;

it('returns the correct plugin id', function () {
    expect((new FilamentTreeViewPlugin())->getId())->toBe('filament-tree-view');
});

it('make() returns a plugin instance', function () {
    expect(FilamentTreeViewPlugin::make())->toBeInstanceOf(FilamentTreeViewPlugin::class);
});
