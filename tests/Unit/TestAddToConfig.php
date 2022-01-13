<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Unit;

use Quillstack\DI\Container;
use Quillstack\DI\Exceptions\InterfaceDefinitionNotFoundException;
use Quillstack\DI\Tests\Mocks\Config\MockClass;
use Quillstack\DI\Tests\Mocks\Config\MockInterface;
use Quillstack\UnitTests\AssertEqual;
use Quillstack\UnitTests\AssertExceptions;

class TestAddToConfig
{
    private Container $container;

    public function __construct(private AssertExceptions $assertExceptions, private AssertEqual $assertEqual)
    {
        $this->container = new Container();
    }

    public function expectNoInterfaceInConfig()
    {
        $this->assertExceptions->expect(InterfaceDefinitionNotFoundException::class);

        $this->container->get(MockInterface::class);
    }

    public function addToConfig()
    {
        $this->container->addToConfig([
            MockInterface::class => MockClass::class,
        ]);

        $mockClass = $this->container->get(MockInterface::class);

        $this->assertEqual->equal($mockClass, new MockClass());
    }
}
