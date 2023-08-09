<?php
namespace Base;

class Application
{
    private $route;
    /** @var AstractController */
    private $controller;
    private $actionName;
    
    public function __construct()
    {
        $this->route = new Route();
    }
    
    public function run()
    {
        $view = new View();

        try {
            $this->route->dispatch($_SERVER['REQUEST_URI']);
            $controller = $this->route->getController();
            $action = $this->route->getAction();
            $controller->setView($view);

            $session = new Session();
            $session->init();
            $controller->setSession($session);
            $result = $controller->$action();
            echo $result;
            
        } catch (RedirectException $e) {
            header('Location: ' . $e->getUrl());
        } catch (Route404Exception $e) {
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
            echo 'Page not found';
        }
    }
}