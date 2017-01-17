# Dingo Adapter for Lumen
Using Dingo + JWT in your Lumen Based Application with no pain.

### Installation

```
composer require krisanalfa/lumen-dingo-adapter
```

### Configuration

In your `bootstrap/app.php` file add this line:

```php
$app->register(Zeek\LumenDingoAdapter\Providers\LumenDingoAdapterServiceProvider::class);
```

Below is environment variable you should configure to make this package works out of the box:

```env
API_PREFIX=api
```

### Guarding Your Routes via Dingo Routing

```php
$app->make(Dingo\Api\Routing\Router::class)->version('v1', function ($api) {
    $api->group([
        'middleware' => 'api.auth',
    ], function ($api) {
        $api->get('/', 'App\Http\Controllers\DefaultController@index');
    });
});
```

### Quick Start

I have made a boilerplate [here](https://github.com/krisanalfa/lumen-jwt). Read the docs there to find out how to _Quick Start_ this package.


### LICENSE
Copyright 2017 Krisan Alfa Timur

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
