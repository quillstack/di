<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Unit\InstanceFactories;

use Quillstack\DI\Container;
use Quillstack\DI\InstanceFactories\ClassFromInterfaceFactory;
use Quillstack\DI\Tests\Mocks\Logger\MockLogger;
use Quillstack\DI\Tests\Mocks\Logger\MockLoggerController;
use Quillstack\DI\Tests\Mocks\Logger\MockLoggerInterface;
use Quillstack\UnitTests\AssertEqual;
use Quillstack\UnitTests\Types\AssertObject;

class TestClassFromInterfaceFactory
{
    private Container $container;

    public function __construct(
        private ClassFromInterfaceFactory $factory,
        private AssertEqual $assertEqual,
        private AssertObject $assertObject
    ) {
        $this->container = new Container([
            MockLoggerInterface::class => MockLogger::class,
        ]);
    }

    public function settingContainer()
    {
        $factory = $this->factory->setContainer($this->container);

        $this->assertEqual->equal($this->factory, $factory);
    }

    public function creatingInstanceFromInterface()
    {
        $controller = $this->container->get(MockLoggerController::class);

        $this->assertObject->instanceOf(MockLoggerController::class, $controller);
        $this->assertObject->notNull($controller->logger);
        $this->assertObject->instanceOf(MockLogger::class, $controller->logger);
    }
}
