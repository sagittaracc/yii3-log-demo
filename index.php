<?php

use Arutyunyan\MyApp\Controllers\Controller;
use Sagittaracc\Router\Route;

require 'vendor/autoload.php';

$route = new Route(
    url: $_SERVER['REQUEST_URI'],
    method: $_SERVER['REQUEST_METHOD']
);

return $route->runIn(Controller::class);