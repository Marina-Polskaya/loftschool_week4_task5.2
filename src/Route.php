<?php
namespace Base;

class Route
{
    private $routes;
    private $controller;
    private $action;

    public function addRoute(string $uri, $controllerName, $actionName = 'index')
    {
        $this->routes[$uri] = [$controllerName, $actionName];
    }
    
    public function auto($uri) : bool
    {
        $parts = explode('/', $uri);
        if (empty($parts[1])) {
            return false;
        }
        $controllerName = $parts[1];
        $actionName = 'index';
        if(isset($parts[2])) {
            $actionName = $parts[2];
        }
        $controllerClassName = 'App\\Controller\\' . ucfirst(strtolower($controllerName));
        if (!class_exists($controllerClassName)) {
            return false;
        }
         $this->controller = new $controllerClassName();
         if (!method_exists($this->controller, $actionName)) {
            return false;
        }

        $this->action = $actionName;
        return true;
    }
    
    public function dispatch(string $uri)
    {
        $parsed = parse_url($uri);
        $path = $parsed['path'];
        if (isset($this->routes[$path])) {
            $this->controller = new $this->routes[$path][0];
            $this->action = $this->routes[$path][1];
            return;
        }
        if (!$this->auto($uri)) {
            if($uri == '/') {
               $this->controller = new \App\Controller\User();
               $this->action = 'login';
               return; 
            } else {
            throw new Route404Exception();
            }
        }
    }
    
    public function getController()
    {
        return $this->controller;
    }
    
    public function getAction()
    {
        return $this->action;
    }
}