<?php

namespace Sagittaracc\Router\tests;

use Sagittaracc\PhpPythonDecorator\Decorator;
use Sagittaracc\Router\Route;

class Controller
{
    use Decorator;

    #[Route('/hello/(\w+)')]
    function greetings($name)
    {
        return "Hello, $name!";
    }
}