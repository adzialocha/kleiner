<?php

namespace Kleiner;

use AltoRouter;
use Medoo\Medoo;
use ReflectionClass;

use Kleiner\Request;
use Kleiner\Response;
use Kleiner\Service;
use Kleiner\Utils\MarkupMinifier;

class Kleiner
{
    protected $config;

    protected $router;

    protected $service;

    protected $request;

    protected $response;

    protected $db;

    public function __construct ($viewsPath, $config)
    {
        $this->config = $config;

        // Prepare router
        $this->router = new AltoRouter();

        // Prepare service instance
        $this->service = new Service($this->config);
        $this->service->setViewsBasePath($viewsPath);

        // Create a Request and Response instance for controllers
        $this->request = new Request($this->config);
        $this->response = new Response($this->config);

        // Establish a database connection
        $this->db = new Medoo($this->config['db']);
    }

    public function dispatch ()
    {
        // Prepare output
        ob_start();

        // Dispatch router
        $this->matchRoute();

        // Minify output when needed
        $output = ob_get_clean();

        if ($this->config['env'] == 'production') {
            $output = MarkupMinifier::convert($output);
        }

        return $output;
    }

    public function setupRoutes ($baseClass, $routesConfig)
    {
        foreach ($routesConfig as $routeConfig) {

            $controller = $baseClass .  $routeConfig['controller'];
            $path = $routeConfig['path'];

            if (array_key_exists('actions', $routeConfig)) {

                foreach ($routeConfig['actions'] as $routeConfigAction) {

                    if (array_key_exists('path', $routeConfigAction)) {
                        $extraPath = $path . $routeConfigAction['path'];
                    } else {
                        $extraPath = $path;
                    }

                    $this->addRoute($controller, $extraPath, $routeConfigAction);

                }

            } else {
                $this->addRoute($controller, $path, $routeConfig);
            }

        }
    }

    private function addRoute ($controller, $path, $config)
    {
        if (array_key_exists('method', $config)) {
            $method = $config['method'];
        } else {
            $method = 'GET';
        }

        $this->router->map($method, $path, array($controller, $config['action']));
    }

    private function getController ($className)
    {
        $class = new ReflectionClass($className);

        return $class->newInstanceArgs([
            $this->config,
            $this->db,
        ]);
    }

    private function matchRoute ()
    {
        // Get match from router
        $match = $this->router->match();

        $this->request->setRoute($match);

        // Identify controller, otherwise use ErrorController
        if ($match && $match['target']) {
            $controllerName = $match['target'][0];
            $actionName = $match['target'][1];
        } else {
            // @TODO
            $controllerName = 'ErrorController';
            $actionName = 'notFoundError';
        }

        // Create controller and call its action method
        $controller = $this->getController($controllerName);

        call_user_func_array(array($controller, $actionName), [
            $this->service,
            $this->request,
            $this->response,
        ]);
    }
}
