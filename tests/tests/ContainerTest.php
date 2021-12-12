<?php

declare(strict_types=1);

namespace Quillstack\Tests\DI;

use PHPUnit\Framework\TestCase;
use Quillstack\DI\Container;
use Quillstack\DI\Exceptions\ClassLoopException;
use Quillstack\DI\Exceptions\UnableToCreateReflectionClassException;
use Quillstack\DI\Tests\Mocks\ContainerItself\MockFactory;
use Quillstack\DI\Tests\Mocks\Database\MockDatabase;
use Quillstack\DI\Tests\Mocks\Loop\MockA;
use Quillstack\DI\Tests\Mocks\Loop\MockC;
use Quillstack\DI\Tests\Mocks\NoLoop\MockA as NoLoopMockA;
use Quillstack\DI\Tests\Mocks\NoLoop\MockB as NoLoopMockB;
use Quillstack\DI\Tests\Mocks\NoLoop\MockC as NoLoopMockC;
use Quillstack\DI\Tests\Mocks\Simple\MockController;

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
