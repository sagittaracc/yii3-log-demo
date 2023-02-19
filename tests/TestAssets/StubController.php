<?php

namespace Arutyunyan\MyApp\Tests\TestAssets;

use Arutyunyan\MyApp\App;
use Arutyunyan\MyApp\Controllers\Controller;
use Psr\Log\LoggerInterface;

class StubController extends Controller
{
    public App $_app;

    public function __construct(LoggerInterface $logger)
    {
        $this->_app = new App($logger);
    }
}