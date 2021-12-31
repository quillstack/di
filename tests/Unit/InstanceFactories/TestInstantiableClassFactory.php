<?php

declare(strict_types=1);

namespace Quillstack\DI\Tests\Unit\InstanceFactories;

use Quillstack\DI\Container;
use Quillstack\DI\InstanceFactories\InstantiableClassFactory;
use Quillstack\DI\Tests\Mocks\Database\MockDatabase;
use Quillstack\DI\Tests\Mocks\Database\MockDatabaseController;
use Quillstack\DI\Tests\Mocks\FirstConfig\MockFirstFactory;
use Quillstack\DI\Tests\Mocks\FirstConfig\MockNoConfigForFactory;
use Quillstack\DI\Tests\Mocks\Object\Logger;
use Quillstack\DI\Tests\Mocks\Object\Service;
use Quillstack\DI\Tests\Mocks\Optional\MockOptionalController;
use Quillstack\DI\Tests\Mocks\ParameterConfig\MockConfig;
use Quillstack\DI\Tests\Mocks\ParameterConfig\MockNoTypeConfig;
use Quillstack\DI\Tests\Mocks\Properties\MockProperties;
use Quillstack\DI\Tests\Mocks\Simple\MockController;
use Quillstack\DI\Tests\Mocks\Simple\MockRepository;
use Quillstack\DI\Tests\Mocks\Simple\MockService;
use Quillstack\DI\Tests\Mocks\Object\LoggerInterface;
use Quillstack\UnitTests\AssertEqual;
use Quillstack\UnitTests\Types\AssertObject;

class TestInstantiableClassFactory
{
    private const HOSTNAME = '127.0.0.1';
    private const USER = 'root';
    private const PASSWORD = '';
    private const DATABASE = 'test';
    private InstantiableClassFactory $factory;
    private Container $container;

    public function __construct(private AssertEqual $assertEqual, private AssertObject $assertObject)
    {
        $logger = new Logger();
        $logger->value = 3;
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
            Logger::class => $logger,
            LoggerInterface::class => $logger,
        ]);
    }

    public function creatingWithParameterWithNoType()
    {
        $config = $this->container->get(MockNoTypeConfig::class);

        $this->assertEqual->equal('default', $config->content);
    }

    public function settingContainer()
    {
        $factory = $this->factory->setContainer($this->container);

        $this->assertEqual->equal($this->factory, $factory);
    }

    public function creatingSimpleInstance()
    {
        $controller = $this->container->get(MockController::class);

        $this->assertObject->instanceOf(MockController::class, $controller);
        $this->assertObject->notNull($controller->service);
        $this->assertObject->instanceOf(MockService::class, $controller->service);
        $this->assertObject->notNull($controller->service->repository);
        $this->assertObject->instanceOf(MockRepository::class, $controller->service->repository);
    }

    public function creatingInstanceWithParameters()
    {
        $controller = $this->container->get(MockDatabaseController::class);

        $this->assertObject->instanceOf(MockDatabaseController::class, $controller);
        $this->assertObject->notNull($controller->database);
        $this->assertObject->instanceOf(MockDatabase::class, $controller->database);
        $this->assertEqual->equal(self::HOSTNAME, $controller->database->hostname);
        $this->assertEqual->equal(self::USER, $controller->database->user);
        $this->assertEqual->equal(self::PASSWORD, $controller->database->password);
        $this->assertEqual->equal(self::DATABASE, $controller->database->database);
    }

    public function creatingInstanceWithOptionalParameters()
    {
        $controller = $this->container->get(MockOptionalController::class);

        $this->assertEqual->equal(MockOptionalController::NAME, $controller->name);
    }

    public function creatingFromProperties()
    {
        $properties = $this->container->get(MockProperties::class);

        $this->assertObject->instanceOf(MockDatabase::class, $properties->getDatabase());
    }

    public function creatingWithConfig()
    {
        $config = $this->container->get(MockConfig::class);

        $this->assertEqual->equal('config', $config->test);
    }

    public function firstConfigThenDefaultValue()
    {
        $factory = $this->container->get(MockFirstFactory::class);
        $factoryNoConfig  = $this->container->get(MockNoConfigForFactory::class);

        $this->assertEqual->equal(0, $factory->level);
        $this->assertEqual->equal(300, $factoryNoConfig->level);
    }

    public function creatingWithConfigAndObjects()
    {
        $service = $this->container->get(Service::class);
        $this->assertEqual->equal(3, $service->logger->value);
        $this->assertEqual->equal(3, $service->loggerFromInterface->value);
    }
}
