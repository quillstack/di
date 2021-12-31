<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Unit;

use Quillstack\DI\Container;
use Quillstack\DI\Tests\Mocks\ParameterConfig\MockConfig;
use Quillstack\UnitTests\AssertEqual;
use Quillstack\UnitTests\Types\AssertBoolean;
use Quillstack\UnitTests\Types\AssertObject;

class TestContainerHelper
{
    public function __construct(
        private AssertObject $assertObject,
        private AssertBoolean $assertBoolean,
        private AssertEqual $assertEqual
    ) {
        //
    }

    public function testEmpty()
    {
        new Container();
        $container = \container();

        $this->assertObject->instanceOf(Container::class, $container);
        $this->assertBoolean->isFalse(
            $container->has(MockConfig::class)
        );
    }

    public function testHelper()
    {
        $container = \container();
        $config = $container->get(MockConfig::class);

        $this->assertBoolean->isTrue(
            $container->has(MockConfig::class)
        );
        $this->assertObject->instanceOf(MockConfig::class, $config);
        $this->assertEqual->equal('default', $config->test);
    }
}
