<?php

namespace Sequence\Routing;


use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;


/**
 * Class RouterCompiler
 * @package Sequence\Routing
 */
class RouterCompiler
{
    private $collection;
    private $routes;
    private static $availableKeys = array(
        'path', 'options', 'defaults', 'requirements'
    );

    /**
     * @param array $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function getCompiledRoutes()
    {
        if(null == $this->collection)
        {
            $this->collection = $this->load();
        }

        return $this->collection;
    }

    protected function load()
    {
        $collection = new RouteCollection();

        foreach($this->routes as $routeName => $routeOptions)
        {
            $routeOptions = $this->normalizeRouteConfig($routeOptions);

            $compiledRoute = $this->parseRoute($routeName, $routeOptions);
            $compiledRoute->compile();

            $collection->add($routeName, $compiledRoute);
        }

        return $collection;
    }

    protected function parseRoute($routeName, $routeOptions)
    {
        $defaults = isset($routeOptions['defaults']) ? $routeOptions['defaults'] : array();
        $requirements = isset($routeOptions['requirements']) ? $routeOptions['requirements'] : array();
        $options = isset($routeOptions['options']) ? $routeOptions['options'] : array();

        if(!isset($routeOptions['path']))
        {
            throw new \InvalidArgumentException(sprintf('You must define a "path" for the "%s" route.', $routeName));
        }

        return new Route($routeOptions['path'], $defaults, $requirements, $options);
    }


    /**
     * @param array $routeOptions
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function normalizeRouteConfig(array $routeOptions)
    {
        foreach($routeOptions as $key => $value)
        {
            if(!in_array($key, self::$availableKeys))
            {
                throw new \InvalidArgumentException(sprintf('RouterCompiler does not support given key: "%s". Expected one of the (%s).', $key, implode(', ', self::$availableKeys)));
            }
        }

        return $routeOptions;
    }
}