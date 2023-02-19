<?php declare(strict_types=1);

use Arutyunyan\MyApp\App;
use Arutyunyan\MyApp\Controllers\Controller;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sagittaracc\Router\Route;

final class ControllerTest extends TestCase
{
    public function testController(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->exactly(2))
            ->method('info')
            ->with('Login: {user}', ['user' => 'test']);

        $controller = new class($logger) extends Controller {
            public App $_app;

            function __construct(LoggerInterface $logger)
            {
                $this->_app = new App($logger);
            }
        };

        // Вызов метода напрямую
        $controller->login('test');

        // Вызов через атрибут метода
        (new Route('/login/test'))->runIn($controller);
    }
}