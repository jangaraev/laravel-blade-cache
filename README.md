# laravel-blade-cache

A small package to cache blade views.

## Installation

```bash
$ composer require jangaraev/laravel-blade-cache
```

Once installed, you have to publish the config file.

```bash
$ php artisan vendor:publish --provider="Jangaraev\LaravelBladeCache\ServiceProvider" --tag="config"
```

This will create the `blade-cache` config file.

## Usage

In Blade files call the `@cache` view helper where needed.

```blade
// before
@include('components.homepage.categories')

// after
@cache('homepage_categories')
```

Then create an appropriate config file record:

```php
    'homepage_categories' => [
        'view' => 'components.homepage.categories', // blade file is referenced here
        'ttl' => 90, // cache TTL in minutes
        'variables' => fn () => [ // closure to collect variables used in blade file
            //'foo' => \App\Models\Foo::get()
        ]
    ],
```

## License

Package is an open-sourced laravel package licensed under the MIT license.