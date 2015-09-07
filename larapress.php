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

class Larapress
{
    protected $capsule = null;

    protected $app = null;

    protected $context = null;

    public function __construct()
    {
        add_action('init', array($this, 'init'));
    }

    public function init()
    {
        $this->setupCapsule();
        $this->setupPaths();
        $this->setupApplication();
        $this->setupRouting();


        $this->context =  Larapress\Context\Context::getInstance();
        // add_action('pre_get_posts', array($this->context,'preGetPosts'));

    }

    protected function setupCapsule()
    {
        $this->capsule = new Capsule;

        $this->capsule->addConnection(array(
            'driver'    => 'mysql',
            'host'      => DB_HOST,
            'database'  => DB_NAME,
            'username'  => DB_USER,
            'password'  => DB_PASSWORD,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => $table_prefix
        ));

        $this->capsule->bootEloquent();
    }

    protected function setupPaths()
    {
        $basePath = str_finish(get_template_directory(), '/');
        $controllersDirectory = $basePath . 'controllers';
        $modelsDirectory = $basePath . 'models';
        $contextDirectory = $basePath . 'context';

        Illuminate\Support\ClassLoader::register();
        Illuminate\Support\ClassLoader::addDirectories(array(
            $controllersDirectory,
            $modelsDirectory,
            $contextDirectory,
        ));
    }

    protected function setupApplication()
    {
        $this->app = new Illuminate\Container\Container;
        Illuminate\Support\Facades\Facade::setFacadeApplication($this->app);

        $this->app['app'] = $this->app;
        $this->app['env'] = 'production';

        with(new Illuminate\Events\EventServiceProvider($this->app))->register();
        with(new Illuminate\Routing\RoutingServiceProvider($this->app))->register();
    }

    protected function setupRouting()
    {
        if (file_exists($basePath . 'routes.php')) {
            try {
                require $basePath . 'routes.php';

                $request = Illuminate\Http\Request::createFromGlobals();
                $response = $this->app['router']->dispatch($request);

                $response->send();

                exit(); // exit to skip other wordpress output
            } catch (NotFoundHttpException $e) {
                // just ignore 404 errors here
            }
        }
    }

    public function getApp()
    {
        return $this->app;
    }

    /**
     * removes from an array of parameters, the parameters that are not needed to call a specific controller action
     *
     * @param  Controller $controller
     * @param  string $action
     * @param  array $params
     * @return array
     */
    protected function reflectParameters($controller, $action, $params)
    {
        $reflection = new ReflectionClass($controller);
        $method = new \ReflectionMethod($controller, $action);

        $passed_params = array();
        foreach ($method->getParameters() as $method_param) {
            if (isset($params[$method_param->getName()])) {
                $passed_params[$method_param->getName()] = $params[$method_param->getName()];
            }
        }

        return $passed_params;
    }

    public function render($controller, $params = array())
    {
        $controller_parts = explode('@', $controller);
        $controller_name = $controller_parts[0];
        $action = $controller_parts[1];

        if (!class_exists($controller_name)) {
            throw new Larapress\Exceptions\ControllerNotFoundException();
        }

        if(!is_callable($controller_name."::".$action)) {
            throw new Larapress\Exceptions\ControllerMethodNotFoundException();
        }

        $controller = $this->getApp()->make($controller_name);

        $passed_params = $this->reflectParameters($controller, $action, $params);

        return $controller->callAction($action, $passed_params);
    }
}

$Larapress = new Larapress();
