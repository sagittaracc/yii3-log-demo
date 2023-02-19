<?php

namespace Arutyunyan\MyApp;

use Psr\Log\LoggerInterface;

class App
{
    function __construct(
        public LoggerInterface $logger,
    ) {}
}