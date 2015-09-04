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

require_once __DIR__ . '/database.php';

function init_larapress()
{
    $basePath = str_finish(get_template_directory(), '/');
    $controllersDirectory = $basePath . 'controllers';
    $modelsDirectory = $basePath . 'models';
    $app = new Illuminate\Container\Container;

    $app['app'] = $app;
    $app['env'] = (defined(LARAPRESS_ENV)) ? LARAPRESS_ENV : 'development';

    Illuminate\Support\ClassLoader::register();
    Illuminate\Support\ClassLoader::addDirectories(array($controllersDirectory, $modelsDirectory));
    Illuminate\Support\Facades\Facade::setFacadeApplication($app);

    with(new Illuminate\Events\EventServiceProvider($app))->register();
    with(new Illuminate\Routing\RoutingServiceProvider($app))->register();

    // include the default Larapress models
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