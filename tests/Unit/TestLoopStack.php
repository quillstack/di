<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Unit;

use Quillstack\DI\Container;
use Quillstack\DI\Exceptions\InterfaceDefinitionNotFoundException;
use Quillstack\DI\Tests\Mocks\Config\MockInterface;
use Quillstack\DI\Tests\Mocks\Loop\MockA;
use Quillstack\DI\Tests\Mocks\Simple\MockController;
use Quillstack\UnitTests\AssertExceptions;
use Quillstack\UnitTests\Types\AssertBoolean;
use Quillstack\UnitTests\Types\AssertObject;

/**
 * The stack detecting dependency loops has to be left as it was found, whether building
 * worked or not. A class left on it made the next attempt at the same one look like a loop.
 */
class TestLoopStack
{
    public function __construct(
        private AssertObject $assertObject,
        private AssertBoolean $assertBoolean,
        private AssertExceptions $assertExceptions
    ) {
        //
    }

    public function askingTwiceForSomethingUnresolvableSaysTheSameThing()
    {
        $container = new Container();

        for ($attempt = 0; $attempt < 2; $attempt++) {
            try {
                $container->get(MockInterface::class);
            } catch (InterfaceDefinitionNotFoundException) {
                continue;
            }

            $this->assertBoolean->isTrue(false);
        }

        $this->assertBoolean->isTrue(true);
    }

    /**
     * has() resolves the entry to answer, and a failed answer must leave nothing behind.
     */
    public function askingWhetherSomethingExistsLeavesNothingBehind()
    {
        $container = new Container();

        $this->assertBoolean->isFalse($container->has(MockInterface::class));
        $this->assertBoolean->isFalse($container->has(MockInterface::class));
        $this->assertObject->instanceOf(MockController::class, $container->get(MockController::class));
    }

    public function arealLoopIsStillReported()
    {
        $this->assertExceptions->expect(\Quillstack\DI\Exceptions\ClassLoopException::class);

        (new Container())->get(MockA::class);
    }
}
