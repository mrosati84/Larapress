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

function init_larapress()
{
    $basePath = str_finish(get_template_directory(), '/');
    $controllersDirectory = $basePath . 'controllers';

    Illuminate\Support\ClassLoader::register();
    Illuminate\Support\ClassLoader::addDirectories(array($controllersDirectory));

    $app = new Illuminate\Container\Container;
    Illuminate\Support\Facades\Facade::setFacadeApplication($app);

    $app['app'] = $app;
    $app['env'] = 'production';

    with(new Illuminate\Events\EventServiceProvider($app))->register();
    with(new Illuminate\Routing\RoutingServiceProvider($app))->register();

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