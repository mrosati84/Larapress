# Larapress

Larapress is a Wordpress plugin that gives you the power of Laravel routing and controllers.

To use it, simply copy it in your plugins directory and enable it in Wordpress plugins section. Next step is to create a `routes.php` file inside your theme directory. This file must follow this structure

```php
<?php
    $app['router']->get('/sample-route', 'MyController@index');
    $app['router']->post('/sample-route', 'MyController@othermethod');
```

You can also use closure-style routes

```php
<?php
    $app['router']->get('sample-route', function () {
        return "Hello!";
    });
```

Route parameters are supported, in the standard Laravel way

```php
<?php
    $app['router']->get('/hello/{name}', 'TestController@hello');
```

## Using controllers

Controllers are located in the `controllers` directory in your theme folder. All controllers classes are automatically loaded by the class loader, and they must be structured like this:

```php
<?php

// Illuminate\Routing\Controller class namespace
// path must be explicit since we are not inside
// a real Laravel context

class TestController extends Illuminate\Routing\Controller {

    public function hello($name)
    {
        return "Hello, " . $name;
    }

}
```

If you want to access the request object, simply pull in the Laravel `Request` class

```php
<?php

use Illuminate\Http\Request;

class ProvaController extends Illuminate\Routing\Controller {

    public function index()
    {
        $request = Request::createFromGlobals();

        // now access the request object
        // using the methods defined in
        // http://laravel.com/api/4.2/Illuminate/Http/Request.html
    }

}
```