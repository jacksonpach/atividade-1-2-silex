<?php

namespace App\Controllers\Factory;

use Silex\Application;

use App\Service\ApiService;
use App\Controllers\ApiController;

class ApiControllerFactory
{

    /**
     * @var ApiController
     */
    private $controller;

    public function __construct(Application $app)
    {
        $apiService = new ApiService($app['orm.em']);
        $this->controller = new ApiController($apiService);
    }

    public function getController()
    {
        return $this->controller;
    }
}