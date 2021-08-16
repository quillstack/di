<?php

namespace QuillStack\DI\InstanceFactories;

use PHPUnit\Framework\TestCase;
use QuillStack\DI\Container;
use QuillStack\Mocks\DI\External\ExternalClassInterface;
use QuillStack\Mocks\DI\External\ExternalController;
use QuillStack\Mocks\DI\External\ExternalInstanceFactory;
use QuillStack\Mocks\DI\External\ExternalNextInterface;

final class ExternalInstanceFactoryTest extends TestCase
{
    public function testExternalInstanceFactory()
    {
        $container = new Container([ExternalClassInterface::class => ExternalInstanceFactory::class]);
        $controller = $container->get(ExternalController::class);

        $this->assertEquals('test', $controller->external->test);
    }

    public function testExternalNextInstanceFactory()
    {
        $container = new Container([ExternalNextInterface::class => ExternalInstanceFactory::class]);
        $controller = $container->get(ExternalController::class);

        $this->assertEquals('test', $controller->external->test);
    }
}
