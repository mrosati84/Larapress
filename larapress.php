<?php
/*
Plugin Name: Larapress
Plugin URI: https://github.com/mrosati84/Larapress
Version: 0.0.1
Description: Use Wordpress with the power of the Laravel Framework
Author: Matteo Rosati <rosati.matteo@gmail.com>
Author URI: http://mrosati.it
License: GPL
*/

require_once 'vendor/autoload.php';
require_once 'vendor/illuminate/support/Illuminate/Support/helpers.php';

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection(array(
    'driver'    => 'mysql',
    'host'      => DB_HOST,
    'database'  => DB_NAME,
    'username'  => DB_USER,
    'password'  => DB_PASSWORD,
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => $table_prefix
));

$capsule->bootEloquent();

function init_larapress()
{
    $basePath = str_finish(get_template_directory(), '/');
    $controllersDirectory = $basePath . 'controllers';
    $modelsDirectory = $basePath . 'models';

    Illuminate\Support\ClassLoader::register();
    Illuminate\Support\ClassLoader::addDirectories(array($controllersDirectory, $modelsDirectory));

    $app = new Illuminate\Container\Container;
    Illuminate\Support\Facades\Facade::setFacadeApplication($app);

    $app['app'] = $app;
    $app['env'] = 'production';

    with(new Illuminate\Events\EventServiceProvider($app))->register();
    with(new Illuminate\Routing\RoutingServiceProvider($app))->register();

    require __DIR__ . '/models/Post.php';
    require __DIR__ . '/models/Postmeta.php';

    if (file_exists($basePath . 'routes.php')) {
        try {
            require $basePath . 'routes.php';

            $request = Illuminate\Http\Request::createFromGlobals();
            $response = $app['router']->dispatch($request);

            $response->send();

            exit(); // exit to skip other wordpress output
        } catch (NotFoundHttpException $e) {
            // just ignore 404 errors here
        }
    }
}

add_action('init', 'init_larapress');