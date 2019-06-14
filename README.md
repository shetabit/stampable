# Laravel Stampable

[![Software License][ico-license]](LICENSE.md)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![StyleCI](https://github.styleci.io/repos/169948762/shield?branch=master)](https://github.styleci.io/repos/169948762)
[![Maintainability](https://api.codeclimate.com/v1/badges/e6a80b17298cb4fcb56d/maintainability)](https://codeclimate.com/github/shetabit/stampable/maintainability)
[![Quality Score][ico-code-quality]][link-code-quality]

This is a Laravel Package for adding stamp behaviors into laravel models. This package supports `Laravel 5.2+`.

# List of contents

- [Install](#install)
- [How to use](#how-to-use)
  - [Configure migration](#configure-migration)
  - [Configure Model](#configure-model)
  - [Define stamps](#define-stamps)
  - [Working with stamps](#working-with-stamps)
- [Change log](#change-log)
- [Contributing](#contributing)
- [Security](#security)
- [Credits](#credits)
- [License](#license)

## Install

Via Composer

``` bash
$ composer require shetabit/stampable
```

## Configure Migration

In your migration you must add `timestamp` field per each stamp.

```php
<?php
    // In migration, you must add published_at field like the below if you want to use it as a stamp.
    $table->timestamp('published_at')->nullable();
```

## Configure Model

In your eloquent model add use `HasStamps` trait like the below.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Shetabit\Stampable\Contracts\Stampable;
use Shetabit\Stampable\Traits\HasStamps;

class Category extends Model implements Stampable
{
    use HasStamps;

    //    
}
```

## Define stamps

you can define stamps using `protected stamps` attribute the model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Shetabit\Stampable\Contracts\Stampable;
use Shetabit\Stampable\Traits\HasStamps;

class Category extends Model implements Stampable
{
    use HasStamps;

    protected $stamps = [
        'published' => 'published_at',    
    ];

    //    
}
```

stamps must be in ['stampName' => 'databaseFieldName'] format.

Model creates methods and local scopes for each stamp dynamically.

According to the latest example, now we have the below methods for each `Category` instance.

```php
<?php

/**
 * notice that be have all of this methods and scopes for each stamps.
 * the name of methods will be similar to the stamp's name.
**/

// methods:
$category->markAsPublished(); // press published stamp on this category!
$category->markAsUnpublished(); // Remove stamp mark from this category.

$category->isPublished(); // Determines if this category is published.
$category->isnUnpublished(); // Determinces if this category is Unpublished.

// scopes: you can use scopes to filter your data using stamp status.
Category::published()->get(); // retrieve published datas
Category::unpublished()->get(); // retrieve unpublished datas
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email khanzadimahdi@gmail.com instead of using the issue tracker.

## Credits

- [Mahdi khanzadi][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/shetabit/stampable.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/shetabit/stampable.svg?label=Code%20Quality&style=flat-square

[link-en]: README.md
[link-packagist]: https://packagist.org/packages/shetabit/stampable
[link-code-quality]: https://scrutinizer-ci.com/g/shetabit/stampable
[link-author]: https://github.com/khanzadimahdi
[link-contributors]: ../../contributors
