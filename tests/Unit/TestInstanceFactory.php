<?php

namespace Quillstack\DI\Tests\Unit;

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
use Quillstack\UnitTests\AssertExceptions;

class TestInstanceFactory
{
    private Container $container;

    public function __construct(private AssertExceptions $assertExceptions)
    {
        $this->container = new Container([
            MockLoggerInterface::class => [],
            MockDatabaseInterface::class => 'UnknownClass',
        ]);
    }

    public function exceptionWhileCreatingInstanceWithParameters()
    {
        $this->assertExceptions->expect(ParameterDefinitionNotFoundException::class);

        $this->container->get(MockDatabaseController::class);
    }

    public function exceptionWhenDefinitionNotFound()
    {
        $this->assertExceptions->expect(InterfaceDefinitionNotFoundException::class);

        $this->container->getInstantiableClassForInterface(MockLoggerController::class);
    }

    public function exceptionUnresolvableParameterType()
    {
        $this->assertExceptions->expect(UnresolvableParameterTypeException::class);

        $this->container->get(MockTrait::class);
    }

    public function exceptionWhenInterfaceDefinitionIsNotString()
    {
        $this->assertExceptions->expect(IncorrectClassTypeException::class);

        $this->container->get(MockLoggerInterface::class);
    }

    public function exceptionWhenClassNotFoundForInterface()
    {
        $this->assertExceptions->expect(ClassNotFoundForInterfaceException::class);

        $this->container->get(MockDatabaseInterface::class);
    }
}
