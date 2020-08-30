<?php

declare(strict_types=1);

namespace QuillDI;

use PHPUnit\Framework\TestCase;
use QuillDI\Exceptions\UnableToCreateReflectionClassException;
use QuillDIMocks\Database\MockDatabase;
use QuillDIMocks\Loop\First;
use QuillDIMocks\Simple\MockController;

final class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container();
    }

    public function testHasMethod()
    {
        $this->container->get(MockController::class);
        $hasMockController = $this->container->has(MockController::class);
        $hasMockDatabase = $this->container->has(MockDatabase::class);

        $this->assertTrue($hasMockController);
        $this->assertFalse($hasMockDatabase);
    }

    public function testReflectionException()
    {
        $this->expectException(UnableToCreateReflectionClassException::class);

        $this->container->get('UnknownClass');
    }
}
