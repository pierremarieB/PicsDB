<?php

namespace PicsDB\Framework;

use PicsDB\Framework\Router;
use PicsDB\Framework\Request;
use PicsDB\Framework\Response;
use PicsDB\Application\Def\Controller;
use PicsDB\Application\Images\ImageController;

class FrontController
{
    protected $request;
    protected $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function execute()
    {
        $router = new Router($this->request);
        $router->main();

        $controllerClass = $router->getControllerClass();
        $action = $router->getAction();
        $accountAction = $router->getAccountAction();
        $url = $router->getURL();

        $view = new View($router);
        
        $controller = new $controllerClass($this->request, $this->response, $view);
        $controller->execute($action, $url);
        $this->response->sendResponse($view);
    }
}
