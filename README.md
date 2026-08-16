<p align="center"><img src="resources/images/stamp.jpg?raw=true"></p>

# Laravel Stampable

[![Software License][ico-license]](LICENSE.md)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads on Packagist][ico-download]][link-packagist]
[![Tests][ico-tests]][link-tests]
[![Code Style][ico-code-style]][link-code-style]
[![Static Analysis][ico-static-analysis]][link-static-analysis]
[![Code Coverage][ico-coverage]][link-coverage]

This is a Laravel Package for adding stamp behaviors into laravel models. This package supports `PHP 8.4+` and
`Laravel 12` and `13`.

# List of contents

- [Install](#install)
- [How to use](#how-to-use)
  - [Configure migration](#configure-migration)
  - [Configure Model](#configure-model)
  - [Define stamps](#define-stamps)
  - [Working with stamps](#working-with-stamps)
  - [Unknown stamps](#unknown-stamps)
- [Testing](#testing)
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

# How to use

## Configure Migration

In your migration you must add `timestamp` field per each stamp.

```php
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

stamps must be in `['stampName' => 'databaseFieldName']` format.

## Working with stamps

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

## Unknown stamps

A stamp that the model does not declare throws a `Shetabit\Stampable\Exceptions\StampNotFoundException`, and the
message names the stamps that are available:

```php
$post->isStampedBy('nonexistent');
// The stamp [nonexistent] is not defined. Available stamps: published, verified.
```

`hasStamp()` asks the same question without throwing, and `getStampField()` resolves a stamp to the column behind it.

## Testing

Every pull request and every push to `master` is checked by [GitHub Actions][link-actions]: the test suite runs on
PHP 8.4 and 8.5 against Laravel 12 and 13 (both the lowest and the highest supported dependencies), the coding style
is checked with PHP_CodeSniffer, the sources are analysed with PHPStan and the code coverage is measured.

The tests run against real Eloquent models on an in-memory sqlite database through Orchestra Testbench, so the
stamps, the scopes and the dynamic methods are exercised the way an application uses them.

```bash
composer install

composer test           # run the test suite
composer test-coverage  # run the test suite and report code coverage
composer check-style    # check the coding style
composer fix-style      # fix the coding style where possible
composer analyse        # run static analysis
composer ci             # run all of the checks above
```

If you would rather not install PHP on your machine, the shipped `Dockerfile` and `Makefile` run everything inside a
container:

```bash
make test              # run the test suite
make coverage          # run the test suite and report code coverage
make check-style       # check the coding style
make fix-style         # fix the coding style where possible
make analyse           # run static analysis
make ci                # run all of the checks above
make shell             # open a shell inside the container
make help              # list every available target
```

Another PHP version can be used with `make test PHP_VERSION=8.5`.

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
[ico-download]: https://img.shields.io/packagist/dt/shetabit/stampable.svg?color=%23F18&style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-tests]: https://img.shields.io/github/actions/workflow/status/shetabit/stampable/tests.yml?branch=master&label=Tests&style=flat-square
[ico-code-style]: https://img.shields.io/github/actions/workflow/status/shetabit/stampable/code-style.yml?branch=master&label=Code%20Style&style=flat-square
[ico-static-analysis]: https://img.shields.io/github/actions/workflow/status/shetabit/stampable/static-analysis.yml?branch=master&label=Static%20Analysis&style=flat-square
[ico-coverage]: https://img.shields.io/codecov/c/github/shetabit/stampable/master?label=Coverage&style=flat-square

[link-en]: README.md
[link-packagist]: https://packagist.org/packages/shetabit/stampable
[link-actions]: https://github.com/shetabit/stampable/actions
[link-tests]: https://github.com/shetabit/stampable/actions/workflows/tests.yml
[link-code-style]: https://github.com/shetabit/stampable/actions/workflows/code-style.yml
[link-static-analysis]: https://github.com/shetabit/stampable/actions/workflows/static-analysis.yml
[link-coverage]: https://codecov.io/gh/shetabit/stampable
[link-author]: https://github.com/khanzadimahdi
[link-contributors]: ../../contributors
