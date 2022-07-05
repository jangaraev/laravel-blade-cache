# laravel-blade-cache

A small package to cache blade views. It caches the whole contents
of rendered blade file as a regular cache item and then just outputs
it from cache during next requests.

It gives a significant speed & memory usage improvement in case if
a large dataset is to be processed in blade files, typically a large
Eloquent collection with lots of relationships loaded.

## Installation

```bash
$ composer require jangaraev/laravel-blade-cache
```

## Usage

In Blade files call the `@cache` view helper where needed.

Arguments:
1. `$view` - view name
2. `$ttl` - cache ttl, optional, default is one hour (`60`)

```blade
// before
@include('homepage.categories')

// after
@cache('homepage.categories', 30)
```

Then you should use a view composer to pass the data to view: https://laravel.com/docs/views#view-composers

For example, in your `AppServiceProvider`:

```php

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // ...
    
    public function boot()
    {
        View::composer('homepage.categories', function (\Illuminate\View\View $view) {
            $view->with('records', \App\Repositories\Foo::get());
        });
        
        View::composer('components.listings', function (\Illuminate\View\View $view) {
            $view->with([
                'block_title' => __('titles.new'),
                'listings' => \App\Models\FooBar::getRecent(),
                'seeMore' => route('listings.latest')
            ]);
        });
}
```

## License

Package is an open-sourced laravel package licensed under the MIT license.
