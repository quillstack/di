<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Unit\InstanceFactories;

use Quillstack\DI\Container;
use Quillstack\DI\Tests\Mocks\External\ExternalClassInterface;
use Quillstack\DI\Tests\Mocks\External\ExternalController;
use Quillstack\DI\Tests\Mocks\External\ExternalInstanceFactory;
use Quillstack\DI\Tests\Mocks\External\ExternalNextInterface;
use Quillstack\UnitTests\AssertEqual;

class TestExternalInstanceFactory
{
    public function __construct(private AssertEqual $assertEqual)
    {
        //
    }

    public function externalInstanceFactory()
    {
        $container = new Container([ExternalClassInterface::class => ExternalInstanceFactory::class]);
        $controller = $container->get(ExternalController::class);

        $this->assertEqual->equal('test', $controller->external->test);
    }

    public function externalNextInstanceFactory()
    {
        $container = new Container([ExternalNextInterface::class => ExternalInstanceFactory::class]);
        $controller = $container->get(ExternalController::class);

        $this->assertEqual->equal('test', $controller->external->test);
    }
}
