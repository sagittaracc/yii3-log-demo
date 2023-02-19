<?php

namespace Arutyunyan\MyApp;

use Psr\Log\LoggerInterface;
use Yiisoft\Log\Target;

class App
{
    public $log;

    function __construct(
        private LoggerInterface $logger,
        private Target $targer,
    )
    {
        $this->log = new $logger([$targer]);
    }
}