# Tree View for Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cube-agency/filament-tree-view.svg?style=flat-square)](https://packagist.org/packages/cube-agency/filament-tree-view)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/cube-agency/filament-tree-view/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/cube-agency/filament-tree-view/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/cube-agency/filament-tree-view/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/cube-agency/filament-tree-view/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/cube-agency/filament-tree-view.svg?style=flat-square)](https://packagist.org/packages/cube-agency/filament-tree-view)

Filament plugin for showing records in tree view.

## Installation

You can install the package via composer:

```bash
composer require cube-agency/filament-tree-view
```

## Usage
This package uses [NestedSet](https://github.com/lazychaser/laravel-nestedset), you should setup your Model/table according to the readme.

Then add trait to your model

```php
<?php

namespace App\Models;

use CubeAgency\FilamentTreeView\Traits\HasTreeView;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class ForumCategory extends Model
{
    use HasTreeView;
}
```

After that you just need to extend ListRecords resource to use TreeViewRecords

```php
namespace App\Filament\Resources\ForumCategoryResource\Pages;

use App\Filament\Resources\ForumCategoryResource;
use CubeAgency\FilamentTreeView\Resources\Pages\TreeViewRecords;

class ListForumCategories extends TreeViewRecords
{
    protected static string $resource = ForumCategoryResource::class;
}
```
and that's it, you are ready to go.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Dmitrijs Mihailovs](https://github.com/dmitrijs.mihailovs)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
