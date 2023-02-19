<?php declare(strict_types=1);

use Arutyunyan\MyApp\Tests\TestAssets\StubController;
use PHPUnit\Framework\TestCase;
use Sagittaracc\Router\Route;

final class ControllerTest extends TestCase
{
    public function testController(): void
    {
        (new Route('/login/test'))->runIn(StubController::class);
        // Check'нуть DummyTarget ...
    }
}