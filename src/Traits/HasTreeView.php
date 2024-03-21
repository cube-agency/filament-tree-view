<?php

namespace CubeAgency\FilamentTreeView\Traits;

trait HasTreeView
{
    public function isChild(): bool
    {
        return $this->getAttribute('parent_id') !== null;
    }
}
