<?php

declare(strict_types=1);

namespace QuillDI\InstanceFactories;

use PHPUnit\Framework\TestCase;
use QuillDI\Container;
use QuillDIMocks\Database\MockDatabase;
use QuillDIMocks\Database\MockDatabaseController;
use QuillDIMocks\Simple\MockController;
use QuillDIMocks\Simple\MockRepository;
use QuillDIMocks\Simple\MockService;

final class InstantiableClassFactoryTest extends TestCase
{
    private const HOSTNAME = '127.0.0.1';
    private const USER = 'root';
    private const PASSWORD = '';
    private const DATABASE = 'test';
    private InstantiableClassFactory $factory;
    private Container $container;

    protected function setUp(): void
    {
        $this->factory = new InstantiableClassFactory();
        $this->container = new Container([], [
            MockDatabase::class => [
                'hostname' => self::HOSTNAME,
                'user' => self::USER,
                'password' => self::PASSWORD,
                'database' => self::DATABASE,
            ]
        ]);
    }

    public function testSettingContainer()
    {
        $factory = $this->factory->setContainer($this->container);

        $this->assertEquals($this->factory, $factory);
    }

    public function testCreatingSimpleInstance()
    {
        $controller = $this->container->get(MockController::class);

        $this->assertInstanceOf(MockController::class, $controller);
        $this->assertNotNull($controller->service);
        $this->assertInstanceOf(MockService::class, $controller->service);
        $this->assertNotNull($controller->service->repository);
        $this->assertInstanceOf(MockRepository::class, $controller->service->repository);
    }

    public function testCreatingInstanceWithParameters()
    {
        $controller = $this->container->get(MockDatabaseController::class);

        $this->assertInstanceOf(MockDatabaseController::class, $controller);
        $this->assertNotNull($controller->database);
        $this->assertInstanceOf(MockDatabase::class, $controller->database);
        $this->assertNotNull($controller->database->hostname);
        $this->assertEquals(self::HOSTNAME, $controller->database->hostname);
        $this->assertNotNull($controller->database->user);
        $this->assertEquals(self::USER, $controller->database->user);
        $this->assertNotNull($controller->database->password);
        $this->assertEquals(self::PASSWORD, $controller->database->password);
        $this->assertNotNull($controller->database->database);
        $this->assertEquals(self::DATABASE, $controller->database->database);
    }
}
