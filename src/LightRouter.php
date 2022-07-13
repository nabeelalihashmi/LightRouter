<?php

/*
* LightRouter
* Nabeel Ali Hashmi (Icon, TheIconicThing)
*
* @version 1.0.0
* @license MIT
* @author Nabeel Ali Hashmi
* @link https://iconiccodes.com
*/

namespace IconicCodes\LightRouter;

use Exception;
use IconicCodes\LightRouter\IMiddleware;

/**
 * LightRouter
 */
class LightRouter {    
    /**
     * __routes
     *
     * @var array<mixed|array>
     */
    private $__routes = [];    
   
    
    /**
     * __notFound
     *
     * @var callable
     */
    private $__notFound;  

    /**
     * allowOverrideRequestMethod
     *
     * @var bool
     */
    public $allowOverrideRequestMethod = true;    
    /**
     * allowedMethod
     *
     * @var array<string>
     */
    public $allowedMethod = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'];    
    /**
     * overrideSlug
     *
     * @var string
     */
    public $overrideSlug = "__method";    
    /**
     * response_type_interface_name
     *
     * @var string|null
     * 
     */
    public $response_type_interface_name = null;
        
    /**
     * setNotFound
     *
     * @param  callable $notFound
     * @return void
     */
    function setNotFound(callable $notFound) {
        $this->__notFound = $notFound;
    }
    
    /**
     * setRoutes
     *
     * @param  mixed $routes
     * @return void
     */
    public function setRoutes($routes) {
        $this->__routes = $routes;
    }
    
    /**
     * addRoute
     *
     * @param  string $method
     * @param  string $uri
     * @param  callable $callback
     * @param  array<IMiddleware> $beforeRoutes
     * @param  array<IMiddleware> $afterRoutes
     * @return void
     */
    public function addRoute($method, $uri, $callback, $beforeRoutes = [], $afterRoutes = []) {
        $this->__routes[] = [$method, $this->clearRouteName($uri), $callback, $beforeRoutes, $afterRoutes];
    }
    
    /**
     * makeRequestUri
     *
     * @return string
     */
    private function makeRequestUri() {
        $script = $_SERVER['SCRIPT_NAME'];
        $dirname = dirname($script);
        $dirname = $dirname === '/' ? '' : $dirname;
        $basename = basename($script);
        $uri = str_replace([$dirname, $basename], "", $_SERVER['REQUEST_URI']);
        return $this->clearRouteName(explode('?', $uri)[0]);
    }
    
    /**
     * clearRouteName
     *
     * @param  mixed $route
     * @return string
     */
    public function clearRouteName($route = '') {
        $route = trim(preg_replace('~/{2,}~', '/', $route), '/');
        return $route === '' ? '/' : "/{$route}";
    }
    
    /**
     * matchRoute
     *
     * @return mixed
     */
    private function matchRoute() {
        $uri = $this->makeRequestUri();
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($this->allowOverrideRequestMethod == true && $method == 'POST') {
            $method = $_POST[$this->overrideSlug] ?? $method;
        }
        $method = strtoupper($method);

        if (!in_array($method, $this->allowedMethod)) {
            throw new Exception("Method '$method' is not allowed");
        }


        $replacementValues = array();

        foreach ($this->__routes as $index => $listUri) {

            $matchUri = $listUri[1];
            $matchMethod = $listUri[0];

            if ($matchMethod !== $method) {
                continue;
            }

            if (preg_match("#^$matchUri$#", $uri)) {

                $realUri = explode('/', $uri);
                $fakeUri = explode('/', $matchUri);

                foreach ($fakeUri as $key => $value) {
                    if ($value == '.+') {
                        $replacementValues[] = $realUri[$key];
                    }
                }
                return [$listUri, $replacementValues];
            }
        }

        return [];
    }

    
    /**
     * run
     *
     * @return void
     */
    public function run() {
        $route = $this->matchRoute();
        if ($route == []) {
            call_user_func($this->__notFound);
            return;
        }
        $route_data = $route[0];
        $route_params = $route[1];

        $callback = $route_data[2];

        $beforeRoutes = $route_data[3] ?? NULL;
        $afterRoutes = $route_data[4] ?? NULL;

        $is_ok = true;
        if ($beforeRoutes !== NULL) {
            foreach ($beforeRoutes as $middle) {

                $middleware = new $middle;
                if (!$middleware instanceof IMiddleware) {
                    throw new Exception("Invalid Middleware");
                }

                $result = call_user_func_array([$middleware, 'handle'], []);
                if ($this->response_type_interface_name !== null && $result instanceof $this->response_type_interface_name) {
                    call_user_func_array([$result, 'handle'], []);
                }
                $is_ok = $result;
                if ($is_ok == false) {
                    return;
                }
            }
        }
        if (is_array($callback)) {
            $class = $callback[0];
            $method = $callback[1];
            $result = call_user_func_array([new $class, $method], $route_params);
        } else {
            $result = call_user_func($callback, $route_params);
        }

        if ($result == null) {
            return;
        }

        if ($this->response_type_interface_name !== null && $result instanceof $this->response_type_interface_name) {
            call_user_func_array([$result,'handle'], []);
            return;
        }

        if ($afterRoutes !== NULL) {
            foreach ($afterRoutes as $middle) {

                $middleware = new $middle;
                if (!$middleware instanceof IMiddleware) {
                    throw new Exception("Invalid Middleware");
                }

                $result = call_user_func_array([$middleware, 'handle'], []);
                if ($this->response_type_interface_name !== null && $result instanceof $this->response_type_interface_name) {
                    call_user_func_array([$result, 'handle'], []);
                }
                $is_ok = $result;
                if ($is_ok == false) {
                    return;
                }
            }
        }
    }
}
