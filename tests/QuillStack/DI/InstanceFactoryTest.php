<?php

declare(strict_types=1);

namespace QuillStack\DI;

use PHPUnit\Framework\TestCase;
use QuillStack\DI\Exceptions\InterfaceDefinitionNotFoundException;
use QuillStack\DI\Exceptions\ParameterDefinitionNotFoundException;
use QuillStack\DI\Exceptions\UnresolvableParameterTypeException;
use QuillStack\Mocks\DI\Database\MockDatabaseController;
use QuillStack\Mocks\DI\Errors\MockTrait;
use QuillStack\Mocks\DI\Logger\MockLoggerController;

final class InstanceFactoryTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
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
}
