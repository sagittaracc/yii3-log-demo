<?php

namespace Arutyunyan\MyApp;

use Psr\Log\LoggerInterface;
use Yiisoft\Log\Target\File\FileTarget;

class App
{
    public $log;

    function __construct(
        private LoggerInterface $logger,
        private FileTarget $fileTarger,
    )
    {
        $this->log = new $logger([$fileTarger]);
    }
}