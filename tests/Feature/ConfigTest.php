<?php

it('has correct default max_depth', function () {
    expect(config('filament-tree-view.max_depth'))->toBe(1);
});

it('has correct default compact value', function () {
    expect(config('filament-tree-view.compact'))->toBeFalse();
});

it('has correct default has_permissions value', function () {
    expect(config('filament-tree-view.has_permissions'))->toBeTrue();
});

it('has correct default has_user_only_policy value', function () {
    expect(config('filament-tree-view.has_user_only_policy'))->toBeFalse();
});
