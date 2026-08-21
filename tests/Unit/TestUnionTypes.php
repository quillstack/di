<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Unit;

use Quillstack\DI\Container;
use Quillstack\DI\Tests\Mocks\UnionTypes\MockUnionParameter;
use Quillstack\DI\Tests\Mocks\UnionTypes\MockUnionProperty;
use Quillstack\UnitTests\AssertEqual;

/**
 * The container read the name off whatever ReflectionType it was given, and getName() only
 * exists on a single named type. A union or an intersection anywhere in a class it built
 * ended the request with a call to an undefined method.
 */
class TestUnionTypes
{
    public function __construct(private AssertEqual $assertEqual)
    {
        //
    }

    public function aUnionParameterIsReadFromTheConfiguration()
    {
        $container = new Container([
            MockUnionParameter::class => ['limit' => 50],
        ]);

        $this->assertEqual->equal(50, $container->get(MockUnionParameter::class)->limit);
    }

    public function aUnionParameterKeepsItsDefault()
    {
        $this->assertEqual->equal(10, (new Container())->get(MockUnionParameter::class)->limit);
    }

    public function aUnionPropertyIsLeftAlone()
    {
        $this->assertEqual->equal(10, (new Container())->get(MockUnionProperty::class)->limit);
    }
}
