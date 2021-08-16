<?php

declare(strict_types=1);

namespace Quillstack\Tests\DI\InstanceFactories;

use PHPUnit\Framework\TestCase;
use Quillstack\DI\Container;
use Quillstack\DI\InstanceFactories\InstantiableClassFactory;
use Quillstack\Mocks\DI\Database\MockDatabase;
use Quillstack\Mocks\DI\Database\MockDatabaseController;
use Quillstack\Mocks\DI\FirstConfig\MockFirstFactory;
use Quillstack\Mocks\DI\FirstConfig\MockNoConfigForFactory;
use Quillstack\Mocks\DI\Optional\MockOptionalController;
use Quillstack\Mocks\DI\ParameterConfig\MockConfig;
use Quillstack\Mocks\DI\ParameterConfig\MockNoTypeConfig;
use Quillstack\Mocks\DI\Properties\MockProperties;
use Quillstack\Mocks\DI\Simple\MockController;
use Quillstack\Mocks\DI\Simple\MockRepository;
use Quillstack\Mocks\DI\Simple\MockService;

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
        $this->container = new Container([
            MockDatabase::class => [
                'hostname' => self::HOSTNAME,
                'user'     => self::USER,
                'password' => self::PASSWORD,
                'database' => self::DATABASE,
            ],
            MockConfig::class => [
                'test' => 'config',
            ],
            MockFirstFactory::class => [
                'level' => 0,
            ],
        ]);
    }

    public function testCreatingWithParameterWithNoType()
    {
        $config = $this->container->get(MockNoTypeConfig::class);

        $this->assertEquals('default', $config->content);
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

    public function testCreatingInstanceWithOptionalParameters()
    {
        $controller = $this->container->get(MockOptionalController::class);

        $this->assertIsString($controller->name);
        $this->assertEquals(MockOptionalController::NAME, $controller->name);
    }

    public function testCreatingFromProperties()
    {
        $properties = $this->container->get(MockProperties::class);

        $this->assertInstanceOf(MockDatabase::class, $properties->getDatabase());
    }

    public function testCreatingWithConfig()
    {
        $config = $this->container->get(MockConfig::class);

        $this->assertEquals('config', $config->test);
    }

    public function testFirstConfigThenDefaultValue()
    {
        $factory = $this->container->get(MockFirstFactory::class);
        $factoryNoConfig  = $this->container->get(MockNoConfigForFactory::class);

        $this->assertEquals(0, $factory->level);
        $this->assertEquals(300, $factoryNoConfig->level);
    }
}
