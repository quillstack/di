<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Unit;

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
use Quillstack\UnitTests\AssertEqual;
use Quillstack\UnitTests\AssertExceptions;
use Quillstack\UnitTests\Types\AssertBoolean;
use Quillstack\UnitTests\Types\AssertObject;

class TestContainer
{
    private Container $container;

    public function __construct(
        private AssertBoolean $assertBoolean,
        private AssertExceptions $assertExceptions,
        private AssertObject $assertObject,
        private AssertEqual $assertEqual
    ) {
        $this->container = new Container();
    }

    public function hasMethod()
    {
        $this->container->get(MockController::class);
        $hasMockController = $this->container->has(MockController::class);
        $hasMockDatabase = $this->container->has(MockDatabase::class);

        $this->assertBoolean->isTrue($hasMockController);
        $this->assertBoolean->isFalse($hasMockDatabase);
    }

    public function reflectionException()
    {
        $this->assertExceptions->expect(UnableToCreateReflectionClassException::class);

        $this->container->get('UnknownClass');
    }

    public function containerItself()
    {
        $mockFactory = $this->container->get(MockFactory::class);
        $controller = $mockFactory->getController();

        $this->assertObject->instanceOf(MockController::class, $controller);
        $this->assertEqual->equal($this->container, $mockFactory->container);
    }

    public function simpleMock()
    {
        $this->assertExceptions->expect(ClassLoopException::class);

        $this->container->get(MockA::class);
    }

    public function moreComplexSimpleMock()
    {
        $this->assertExceptions->expect(ClassLoopException::class);

        $this->container->get(MockC::class);
    }

    public function stack()
    {
        $mockA = $this->container->get(NoLoopMockA::class);
        $mockB = $this->container->get(NoLoopMockB::class);
        $mockC = $this->container->get(NoLoopMockC::class);

        $this->assertEqual->equal($mockB, $mockA->mock);
        $this->assertEqual->equal($mockB, $mockC->mock);
    }
}
