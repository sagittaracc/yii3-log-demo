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

    #[Di(App::class)]
    private App $app;

    #[Di(Logger::class)]
    private LoggerInterface $logger;

    #[Di(FileTarget::class, [Config::LOG_FILE])]
    private FileTarget $fileTarger;
}