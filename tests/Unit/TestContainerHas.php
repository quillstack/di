<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Unit;

use Quillstack\DI\Container;
use Quillstack\DI\Tests\Mocks\Config\MockClass;
use Quillstack\DI\Tests\Mocks\Config\MockInterface;
use Quillstack\DI\Tests\Mocks\Database\MockDatabase;
use Quillstack\DI\Tests\Mocks\Loop\MockA;
use Quillstack\DI\Tests\Mocks\Simple\MockController;
use Quillstack\UnitTests\Types\AssertBoolean;

/**
 * PSR-11 requires `has($id)` to return `true` whenever `get($id)` would not throw
 * a `NotFoundExceptionInterface`, even if the entry has not been resolved yet.
 */
class TestContainerHas
{
    public function __construct(private AssertBoolean $assertBoolean)
    {
        //
    }

    public function hasResolvableClassBeforeItIsRequested()
    {
        $container = new Container();

        $this->assertBoolean->isTrue($container->has(MockController::class));
    }

    public function hasContainerItself()
    {
        $container = new Container();

        $this->assertBoolean->isTrue($container->has(Container::class));
    }

    public function hasInterfaceOnlyWhenConfigured()
    {
        $withoutConfig = new Container();
        $withConfig = new Container([
            MockInterface::class => MockClass::class,
        ]);

        $this->assertBoolean->isFalse($withoutConfig->has(MockInterface::class));
        $this->assertBoolean->isTrue($withConfig->has(MockInterface::class));
    }

    public function hasNotUnknownClass()
    {
        $container = new Container();

        $this->assertBoolean->isFalse($container->has('Unknown\\Class\\Name'));
    }

    /**
     * A dependency loop means the entry is known but cannot be built, which is a container
     * error rather than a not-found one, so `has()` still answers true.
     */
    public function hasClassWhichIsKnownButFailsToBuild()
    {
        $container = new Container();

        $this->assertBoolean->isTrue($container->has(MockA::class));
    }

    public function hasNotClassWithUnresolvableParameters()
    {
        $container = new Container();

        $this->assertBoolean->isFalse($container->has(MockDatabase::class));
    }
}
