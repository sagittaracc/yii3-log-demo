<?php

namespace Arutyunyan\MyApp\Controllers;

use Arutyunyan\MyApp\Container;
use Sagittaracc\Router\Route;

class Controller
{
    use Container;

    #[Route('/login/(\w+)')]
    function login(string $user)
    {
        $this->_logger->info('Login: {user}', ['user' => $user]);
    }
}