<?php

declare(strict_types=1);

namespace Quillstack\Tests\DI\InstanceFactories;

use PHPUnit\Framework\TestCase;
use Quillstack\DI\Container;
use Quillstack\DI\InstanceFactories\ClassFromInterfaceFactory;
use Quillstack\Mocks\DI\Logger\MockLogger;
use Quillstack\Mocks\DI\Logger\MockLoggerController;
use Quillstack\Mocks\DI\Logger\MockLoggerInterface;

final class ClassFromInterfaceFactoryTest extends TestCase
{
    private ClassFromInterfaceFactory $factory;
    private Container $container;

    protected function setUp(): void
    {
        $this->factory = new ClassFromInterfaceFactory();
        $this->container = new Container([
            MockLoggerInterface::class => MockLogger::class,
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
