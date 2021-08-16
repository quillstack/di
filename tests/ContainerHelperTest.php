<?php

namespace Quillstack;

use PHPUnit\Framework\TestCase;
use Quillstack\DI\Container;
use Quillstack\DI\Exceptions\ContainerNotInitialisedException;
use Quillstack\Mocks\DI\ParameterConfig\MockConfig;

final class ContainerHelperTest extends TestCase
{
    public function testException()
    {
        $this->expectException(ContainerNotInitialisedException::class);

        \container();
    }

    public function testEmpty()
    {
        new Container();
        $container = \container();

        $this->assertInstanceOf(Container::class, $container);
        $this->assertFalse($container->has(MockConfig::class));
    }

    public function testHelper()
    {
        $container = \container();
        $config = $container->get(MockConfig::class);

        $this->assertTrue($container->has(MockConfig::class));
        $this->assertInstanceOf(MockConfig::class, $config);
        $this->assertEquals('default', $config->test);
    }
}
