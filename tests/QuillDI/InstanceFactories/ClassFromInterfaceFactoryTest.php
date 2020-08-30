<?php

declare(strict_types=1);

namespace QuillDI\InstanceFactories;

use PHPUnit\Framework\TestCase;
use QuillDI\Container;
use QuillDIMocks\Logger\MockLogger;
use QuillDIMocks\Logger\MockLoggerController;
use QuillDIMocks\Logger\MockLoggerInterface;

final class ClassFromInterfaceFactoryTest extends TestCase
{
    private ClassFromInterfaceFactory $factory;
    private Container $container;

    protected function setUp(): void
    {
        $this->factory = new ClassFromInterfaceFactory();
        $this->container = new Container([
            MockLoggerInterface::class => MockLogger::class
        ]);
    }

    public function testSettingContainer()
    {
        $factory = $this->factory->setContainer($this->container);

        $this->assertEquals($this->factory, $factory);
    }

    public function testCreatingInstanceFromInterface()
    {
        $controller = $this->container->get(MockLoggerController::class);

        $this->assertInstanceOf(MockLoggerController::class, $controller);
        $this->assertNotNull($controller->logger);
        $this->assertInstanceOf(MockLogger::class, $controller->logger);
    }
}
