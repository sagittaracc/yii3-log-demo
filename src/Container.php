<?php

namespace Arutyunyan\MyApp;

use Arutyunyan\MyApp\Config\Config;
use Psr\Log\LoggerInterface;
use Sagittaracc\Di\Di;
use Sagittaracc\PhpPythonDecorator\Decorator;
use Yiisoft\Log\Logger;
use Yiisoft\Log\Target\File\FileTarget;

trait Container
{
    use Decorator;

    #[Di(Logger::class, [new FileTarget(Config::LOG_FILE)])]
    public LoggerInterface $logger;
}