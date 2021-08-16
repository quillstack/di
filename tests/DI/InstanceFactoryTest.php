<?php

namespace Quillstack\DI;

use PHPUnit\Framework\TestCase;
use Quillstack\DI\Exceptions\ClassNotFoundForInterfaceException;
use Quillstack\DI\Exceptions\IncorrectClassTypeException;
use Quillstack\DI\Exceptions\InterfaceDefinitionNotFoundException;
use Quillstack\DI\Exceptions\ParameterDefinitionNotFoundException;
use Quillstack\DI\Exceptions\UnresolvableParameterTypeException;
use Quillstack\Mocks\DI\Database\MockDatabaseController;
use Quillstack\Mocks\DI\Database\MockDatabaseInterface;
use Quillstack\Mocks\DI\Errors\MockTrait;
use Quillstack\Mocks\DI\Logger\MockLoggerController;
use Quillstack\Mocks\DI\Logger\MockLoggerInterface;

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
