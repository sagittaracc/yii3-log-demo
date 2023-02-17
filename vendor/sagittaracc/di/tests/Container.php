<?php

namespace Sagittaracc\Di\tests;

use Sagittaracc\Di\Di;
use Sagittaracc\Di\tests\services\geo\GeolocationService;
use Sagittaracc\Di\tests\services\geo\GeolocationServiceInterface;
use Sagittaracc\Di\tests\services\proxy\Proxy;
use Sagittaracc\Di\tests\services\proxy\ProxyInterface;
use Sagittaracc\PhpPythonDecorator\Decorator;

trait Container
{
    use Decorator;

    #[Di(GeolocationService::class)]
    private GeolocationServiceInterface $geolocationService;

    #[Di(Proxy::class)]
    private ProxyInterface $proxy;
}