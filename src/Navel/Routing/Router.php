<?php

namespace Navel\Routing;

use Navel\Psr\Http\Message\ServerRequestInterface;

/**
 * Router component to implement URL routing functionnality
 *
 * @author Julien SAGOT
 * @package Navel\Routing
 * @final
 */
final class Router
{
    private $_routes = [];
    private $_namedRoutes = [];

    /**
     * Fucntion Router::addRoute()
     *
     * Add a route description for matching purposes
     *
     * @param string $path Ex: blog/:category-name/:post-uri.
     * @param callable $middleware A middleware callback function.
     * @param string $name The route name.
     * @param string $method Http method for current path (Possibles 'GET', 'POST', 'PUT' or 'DELETE').
     * @return Route
     */
    private function addRoute($path, $middleware, $name, $method = 'GET')
    {
		$route = new Route($path, $middleware, $name);
        $this->_routes[$method][] = $route;
        $this->_namedRoutes[$name] = $route;
        return $route;
    }

    /**
     * Fucntion Router::get()
     *
     * Get a named route.
     *
     * @param string $path Ex: controller/{param:[a-z0-9]+}.
     * @param callable $middleware A middleware callback function.
     * @param string $name The route name.
     * @return Route
     */
    public function get($path, $name, callable $middleware)
    {
        return $this->addRoute($path, $middleware, $name, 'GET');
    }

    /**
     * Fucntion Router::post()
     *
     * Post to a named route.
     *
     * @param string $path Ex: blog/:category-name/:post-uri.
     * @param callable $middleware A middleware callback function.
     * @param string $name The route name.
     * @return Route
     */
    public function post($path, $name, callable $middleware)
    {
        return $this->addRoute($path, $middleware, $name, 'POST');
    }

    /**
     * Fucntion Router::put()
     *
     * Put to a named route.
     *
     * @param string $path Ex: blog/:category-name/:post-uri.
     * @param callable $middleware A middleware callback function.
     * @param string $name The route name.
     * @return Route
     */
    public function put($path, $name, callable $middleware)
    {
        return $this->addRoute($path, $middleware, $name, 'PUT');
    }

    /**
     * Fucntion Router::delete()
     *
     * Delete to a named route.
     *
     * @param string $path Ex: blog/:category-name/:post-uri.
     * @param callable $middleware A middleware callback function.
     * @param string $name The route name.
     * @return Route
     */
    public function delete($path, $name, callable $middleware)
    {
        return $this->addRoute($path, $middleware, $name, 'DELETE');
    }

    /**
     * Fucntion Router::process()
     *
     * Search route for a match.
     *
     * @param Navel\Psr\Http\Message\ServerRequestInterface $request
     * @return Route The matched route.
     */
    public function process(ServerRequestInterface $request)
    {
        $method = $request->getMethod();
        if (!isset($this->_routes[$method])) {
            throw new RouterException('REQUEST_METHOD does not exist.');
        }
        foreach ($this->_routes[$method] as $route) {
            if ($route->match($request->getUri()->getPath())) {
                return $route;
            }
        }
        return null;
    }

    /**
     * Fucntion Router::getUri()
     *
     * Get the route URI identified by name
     *
     * @param string $name Route name to find
     * @param array $params
     * @return string Matched route uri
     * @throws RouterException No matching route
     */
    public function getUri($name, $params = [])
    {
        if (!isset($this->_namedRoutes[$name])) {
            throw new RouterException('Page not found, no matching route for '.$name.'.');
        }
        return $this->_namedRoutes[$name]->getUri($params);
    }
}
