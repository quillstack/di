<?php

declare(strict_types=1);

namespace Quillstack\Tests\DI\InstanceFactories;

use PHPUnit\Framework\TestCase;
use Quillstack\DI\Container;
use Quillstack\DI\Tests\Mocks\External\ExternalClassInterface;
use Quillstack\DI\Tests\Mocks\External\ExternalController;
use Quillstack\DI\Tests\Mocks\External\ExternalInstanceFactory;
use Quillstack\DI\Tests\Mocks\External\ExternalNextInterface;

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
