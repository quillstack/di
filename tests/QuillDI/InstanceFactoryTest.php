<?php

declare(strict_types=1);

namespace QuillDI;

use PHPUnit\Framework\TestCase;
use QuillDI\Exceptions\InterfaceDefinitionNotFoundException;
use QuillDI\Exceptions\ParameterDefinitionNotFoundException;
use QuillDI\Exceptions\UnresolvableParameterTypeException;
use QuillDIMocks\Database\MockDatabaseController;
use QuillDIMocks\Errors\MockTrait;
use QuillDIMocks\Logger\MockLoggerController;

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
