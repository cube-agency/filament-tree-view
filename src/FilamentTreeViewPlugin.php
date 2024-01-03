<?php

namespace CubeAgency\FilamentTreeView;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentTreeViewPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-tree-view';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }
}
