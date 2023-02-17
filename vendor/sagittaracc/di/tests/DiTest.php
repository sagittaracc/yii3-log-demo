<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Sagittaracc\Di\tests\Store;

final class DiTest extends TestCase
{
    public function testDi(): void
    {
        $store = new Store;
        $this->assertSame([33.333, 55.555], $store->getCoordinates());
    }
}