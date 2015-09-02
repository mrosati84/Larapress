# Larapress

Larapress is a Wordpress plugin that gives you the power of Laravel routing and controllers.

To use it, simply copy it in your plugins directory and enable it in Wordpress plugins section. Next step is to create a `routes.php` file inside your theme directory. This file must follow this structure

```php
<?php

$app['router']->get('/sample-route', 'TestController@index');
$app['router']->post('/sample-route', 'TestController@othermethod');
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

## Accessing the request object

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

## Database support

Larapress supports Eloquent models. All model classes in `models` directory inside your root theme folder will be automatically loaded and will be available in your controllers/closure-routes. Let's say you define a `Post` class in `<your-theme>/models/Post.php``

```php
<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class Post extends Eloquent {
    public $timestamps = false;
}
```

Now you can query your Wordpress posts (don't worry, database connection and table prefix is automatically set using Wordpress configuration). To get all your posts just use the `Post` model like you would normally do in Laravel

```php
<?php

class PostsController extends Illuminate\Routing\Controller {

    public function index()
    {
        return Post::where(['post_type' => 'post'])->get();
    }
```