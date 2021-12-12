<?php

namespace Quillstack\Tests\DI;

use PHPUnit\Framework\TestCase;
use Quillstack\DI\Container;
use Quillstack\DI\Exceptions\ClassNotFoundForInterfaceException;
use Quillstack\DI\Exceptions\IncorrectClassTypeException;
use Quillstack\DI\Exceptions\InterfaceDefinitionNotFoundException;
use Quillstack\DI\Exceptions\ParameterDefinitionNotFoundException;
use Quillstack\DI\Exceptions\UnresolvableParameterTypeException;
use Quillstack\DI\Tests\Mocks\Database\MockDatabaseController;
use Quillstack\DI\Tests\Mocks\Database\MockDatabaseInterface;
use Quillstack\DI\Tests\Mocks\Errors\MockTrait;
use Quillstack\DI\Tests\Mocks\Logger\MockLoggerController;
use Quillstack\DI\Tests\Mocks\Logger\MockLoggerInterface;

final class InstanceFactoryTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container([
            MockLoggerInterface::class => [],
            MockDatabaseInterface::class => 'UnknownClass',
        ]);
    }

    public function testExceptionWhileCreatingInstanceWithParameters()
    {
        $this->expectException(ParameterDefinitionNotFoundException::class);

        $this->container->get(MockDatabaseController::class);
    }

    public function testExceptionWhenDefinitionNotFound()
    {
        $this->expectException(InterfaceDefinitionNotFoundException::class);

        $this->container->getInstantiableClassForInterface(MockLoggerController::class);
    }

    public function testExceptionUnresolvableParameterType()
    {
        $this->expectException(UnresolvableParameterTypeException::class);

        $this->container->get(MockTrait::class);
    }

    public function testExceptionWhenInterfaceDefinitionIsNotString()
    {
        $this->expectException(IncorrectClassTypeException::class);

        $this->container->get(MockLoggerInterface::class);
    }

    public function testExceptionWhenClassNotFoundForInterface()
    {
        $this->expectException(ClassNotFoundForInterfaceException::class);

        $this->container->get(MockDatabaseInterface::class);
    }
}
