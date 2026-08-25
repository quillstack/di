<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Unit;

use Psr\Container\NotFoundExceptionInterface;
use Quillstack\DI\Container;
use Quillstack\DI\Tests\Mocks\Optional\MockBrokenCollaborator;
use Quillstack\DI\Tests\Mocks\Optional\MockCollaborator;
use Quillstack\DI\Tests\Mocks\Optional\MockOptionalCollaborator;
use Quillstack\DI\Tests\Mocks\Optional\MockRealCollaborator;
use Quillstack\UnitTests\AssertEqual;
use Quillstack\UnitTests\Types\AssertBoolean;
use Quillstack\UnitTests\Types\AssertNull;

/**
 * A constructor saying `?Thing $thing = null` says the class works without one. The container
 * built every parameter that named a class regardless, so a class was unbuildable until
 * everything it had declared it could do without was registered too.
 */
class TestOptionalDependencies
{
    public function __construct(
        private AssertEqual $assertEqual,
        private AssertNull $assertNull,
        private AssertBoolean $assertBoolean
    ) {
        //
    }

    public function anOptionalCollaboratorNobodyRegisteredIsLeftOut()
    {
        $built = (new Container())->get(MockOptionalCollaborator::class);

        $this->assertNull->isNull($built->collaborator);
    }

    /**
     * The default is not preferred to a registered one: it is only what happens when there
     * is nothing to prefer.
     */
    public function aRegisteredOneIsStillUsed()
    {
        $container = new Container([
            MockCollaborator::class => MockRealCollaborator::class,
        ]);

        $built = $container->get(MockOptionalCollaborator::class);

        $this->assertEqual->equal('the real one', $built->collaborator?->name());
    }

    /**
     * The line between the two cases. Nothing registered is a question the container can
     * answer; a registered thing which throws is a mistake somebody wants to hear about, and
     * turning it into a silent null would move the failure somewhere further away and make it
     * much harder to explain.
     */
    public function somethingRegisteredThatFailsToBuildIsNotSwallowed()
    {
        $container = new Container([
            MockCollaborator::class => MockBrokenCollaborator::class,
        ]);

        $thrown = null;

        try {
            $container->get(MockOptionalCollaborator::class);
        } catch (\Throwable $throwable) {
            $thrown = $throwable;
        }

        $this->assertBoolean->isFalse($thrown === null);
        $this->assertBoolean->isFalse($thrown instanceof NotFoundExceptionInterface);
        $this->assertEqual->equal('this collaborator is broken', $thrown?->getMessage());
    }
}
