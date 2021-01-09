<?php

namespace QuillStack\DI;

use PHPUnit\Framework\TestCase;
use QuillStack\DI\Exceptions\UnableToCreateReflectionClassException;
use QuillStack\Mocks\DI\ContainerItself\MockFactory;
use QuillStack\Mocks\DI\Database\MockDatabase;
use QuillStack\Mocks\DI\Simple\MockController;

final class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testHasMethod()
    {
        $this->container->get(MockController::class);
        $hasMockController = $this->container->has(MockController::class);
        $hasMockDatabase = $this->container->has(MockDatabase::class);

        $this->assertTrue($hasMockController);
        $this->assertFalse($hasMockDatabase);
    }

    public function testReflectionException()
    {
        $this->expectException(UnableToCreateReflectionClassException::class);

        $this->container->get('UnknownClass');
    }

    public function testContainerItself()
    {
        $mockFactory = $this->container->get(MockFactory::class);
        $controller = $mockFactory->getController();

        $this->assertInstanceOf(MockController::class, $controller);
        $this->assertSame($this->container, $mockFactory->container);
    }
}
