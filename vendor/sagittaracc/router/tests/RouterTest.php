<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Sagittaracc\Router\Route;
use Sagittaracc\Router\tests\Controller;

final class RouterTest extends TestCase
{
    public function testRouter(): void
    {
        $this->assertSame('Hello, yuriy!', (new Route('/hello/yuriy'))->runIn(Controller::class));
    }
}