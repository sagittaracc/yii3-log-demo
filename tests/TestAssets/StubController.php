<?php

namespace Arutyunyan\MyApp\Tests\TestAssets;

use Arutyunyan\MyApp\App;
use Arutyunyan\MyApp\Controllers\Controller;
use Yiisoft\Log\Logger;
use Yiisoft\Log\Target\File\FileTarget;

class StubController extends Controller
{
    /**
     * По логике нужно stub'нуть контейнер
     * но контейнер это трейт :)
     * Поэтому stub'им вручную всё что нужно из контейнера
     */
    public App $_app;

    function __construct()
    {
        // Не стал разбираться как сделать DummyTarget
        $this->_app = new App(new Logger(), new FileTarget('log/dummy.log'));
    }
}