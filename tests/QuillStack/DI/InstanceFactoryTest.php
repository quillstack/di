<?php

namespace QuillStack\DI;

use PHPUnit\Framework\TestCase;
use QuillStack\DI\Exceptions\ClassNotFoundForInterfaceException;
use QuillStack\DI\Exceptions\IncorrectClassTypeException;
use QuillStack\DI\Exceptions\InterfaceDefinitionNotFoundException;
use QuillStack\DI\Exceptions\ParameterDefinitionNotFoundException;
use QuillStack\DI\Exceptions\UnresolvableParameterTypeException;
use QuillStack\Mocks\DI\Database\MockDatabaseController;
use QuillStack\Mocks\DI\Database\MockDatabaseInterface;
use QuillStack\Mocks\DI\Errors\MockTrait;
use QuillStack\Mocks\DI\Logger\MockLoggerController;
use QuillStack\Mocks\DI\Logger\MockLoggerInterface;

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
