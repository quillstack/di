<?php

namespace QuillStack\DI;

use PHPUnit\Framework\TestCase;
use QuillStack\DI\Exceptions\ClassLoopException;
use QuillStack\DI\Exceptions\UnableToCreateReflectionClassException;
use QuillStack\Mocks\DI\ContainerItself\MockFactory;
use QuillStack\Mocks\DI\Database\MockDatabase;
use QuillStack\Mocks\DI\Loop\MockA;
use QuillStack\Mocks\DI\Loop\MockC;
use QuillStack\Mocks\DI\NoLoop\MockA as NoLoopMockA;
use QuillStack\Mocks\DI\NoLoop\MockB as NoLoopMockB;
use QuillStack\Mocks\DI\NoLoop\MockC as NoLoopMockC;
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

    public function testSimpleMock()
    {
        $this->expectException(ClassLoopException::class);

        $this->container->get(MockA::class);
    }

    public function testMoreComplexSimpleMock()
    {
        $this->expectException(ClassLoopException::class);

        $this->container->get(MockC::class);
    }

    public function testStack()
    {
        $mockA = $this->container->get(NoLoopMockA::class);
        $mockB = $this->container->get(NoLoopMockB::class);
        $mockC = $this->container->get(NoLoopMockC::class);

        $this->assertSame($mockB, $mockA->mock);
        $this->assertSame($mockB, $mockC->mock);
    }
}
