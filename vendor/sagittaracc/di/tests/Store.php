<?php

namespace Sagittaracc\Di\tests;

class Store
{
    use Container;

    public function getCoordinates()
    {
        return $this->_geolocationService->getCoordinatesFromAddress();
    }
}