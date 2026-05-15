<?php

use CubeAgency\FilamentTreeView\Traits\HasTreeView;

it('returns false when parent_id is null', function () {
    $model = new class {
        use HasTreeView;

        public function getAttribute(string $key): mixed
        {
            return null;
        }
    };

    expect($model->isChild())->toBeFalse();
});

it('returns true when parent_id is set', function () {
    $model = new class {
        use HasTreeView;

        public function getAttribute(string $key): mixed
        {
            return 1;
        }
    };

    expect($model->isChild())->toBeTrue();
});
