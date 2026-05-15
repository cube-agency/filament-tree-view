<?php

namespace CubeAgency\FilamentTreeView\Tests;

use CubeAgency\FilamentTreeView\FilamentTreeViewServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FilamentTreeViewServiceProvider::class,
        ];
    }
}
