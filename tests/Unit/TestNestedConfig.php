<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Unit;

use Quillstack\DI\Container;
use Quillstack\DI\Tests\Mocks\NestedConfig\MockInnerService;
use Quillstack\DI\Tests\Mocks\NestedConfig\MockOuterService;
use Quillstack\UnitTests\AssertEqual;

/**
 * Building a dependency runs through the same factory, which used to leave it pointing at
 * the class it had just built. Every configured value read after that was then looked up
 * under the wrong class name and quietly fell back to its default.
 */
class TestNestedConfig
{
    public function __construct(private AssertEqual $assertEqual)
    {
        //
    }

    public function aValueIsReadAfterADependencyWasBuilt()
    {
        $container = new Container([
            MockOuterService::class => ['name' => 'outer configured'],
            MockInnerService::class => ['name' => 'inner configured'],
        ]);

        $outer = $container->get(MockOuterService::class);

        $this->assertEqual->equal('outer configured', $outer->name);
        $this->assertEqual->equal('inner configured', $outer->inner->name);
    }

    public function defaultsAreKeptWhenNothingIsConfigured()
    {
        $outer = (new Container())->get(MockOuterService::class);

        $this->assertEqual->equal('outer default', $outer->name);
        $this->assertEqual->equal('inner default', $outer->inner->name);
    }
}
