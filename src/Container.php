<?php

declare(strict_types=1);

namespace Quillstack\DI;

use Psr\Container\ContainerInterface;
use Quillstack\DI\Exceptions\ClassLoopException;
use Quillstack\DI\Exceptions\ContainerNotInitialisedException;
use Quillstack\DI\Exceptions\IncorrectClassTypeException;
use Quillstack\DI\Exceptions\ClassNotFoundForInterfaceException;
use Quillstack\DI\Exceptions\InterfaceDefinitionNotFoundException;
use Quillstack\DI\Exceptions\UnableToCreateReflectionClassException;
use Quillstack\DI\InstanceFactories\ClassFromInterfaceFactory;
use Quillstack\DI\InstanceFactories\InstantiableClassFactory;
use ReflectionException;

/**
 * The Dependency Injection Container.
 */
class Container implements ContainerInterface
{
    /**
     * Instances array.
     */
    private array $instances = [];

    /**
     * Current classes stack to detect loops.
     */
    private array $stack = [];

    /**
     * Instance factory to create new instances.
     */
    private InstanceFactory $instanceFactory;

    /**
     * Configuration for interfaces and parameters.
     */
    private array $config;

    private static Container $instance;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->instanceFactory = new InstanceFactory(
            $this,
            new InstantiableClassFactory(),
            new ClassFromInterfaceFactory()
        );

        Container::$instance = $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if ($id === Container::class) {
            return $this;
        }

        if (!isset($this->instances[$id])) {
            $this->createNewInstance($id);
        }

        return $this->instances[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function has($id): bool
    {
        return isset($this->instances[$id]);
    }

    /**
     * Method to create a new instance.
     */
    private function createNewInstance(string $id): void
    {
        if (in_array($id, $this->stack, true)) {
            throw new ClassLoopException("Class `{$id}` is in a loop", 500);
        }

        $this->stack[] = $id;

        try {
            $this->instances[$id] = $this->instanceFactory->create($id);
        } catch (ReflectionException $exception) {
            array_pop($this->stack);
            $message = "Unable to create reflection class for `{$id}`";

            throw new UnableToCreateReflectionClassException($message, 500, $exception);
        } catch (\Exception $exception) {
            array_pop($this->stack);

            throw $exception;
        }

        Container::$instance = $this;
    }

    /**
     * Gets a class name for the given interface from the configuration.
     */
    public function getInstantiableClassForInterface(string $interface): string
    {
        if (!isset($this->config[$interface])) {
            throw new InterfaceDefinitionNotFoundException("Interface definition `{$interface}` not found");
        }

        $className = $this->config[$interface];

        if (!is_string($className)) {
            throw new IncorrectClassTypeException("Incorrect class type for interface `{$interface}`");
        }

        if (!class_exists($className)) {
            throw new ClassNotFoundForInterfaceException("Class not found for interface `{$interface}`");
        }

        return $className;
    }

    /**
     * Gets a parameter value for the given name from the configuration.
     */
    public function getParameterForClass(string $className, string $parameterName): mixed
    {
        if (!isset($this->config[$className][$parameterName])) {
            return null;
        }

        return $this->config[$className][$parameterName];
    }

    public function getCustomFactoryClassName(string $classNameOrInterface): ?string
    {
        if (!class_exists($classNameOrInterface) && !interface_exists($classNameOrInterface)) {
            return null;
        }

        if (isset($this->config[$classNameOrInterface])) {
            return $this->getPotentialClassFactory($classNameOrInterface);
        }

        $interfaces = class_implements($classNameOrInterface);
        $configKeys = array_keys($this->config);

        foreach ($interfaces as $interface) {
            if (false === ($key = array_search($interface, $configKeys, true))) {
                continue;
            }

            if ($configKeys[$key] !== $interface) {
                continue;
            }

            if ($potentialClassFactory = $this->getPotentialClassFactory($interface)) {
                return $potentialClassFactory;
            }
        }

        return null;
    }

    private function getPotentialClassFactory(string $id): ?string
    {
        $class = $this->config[$id];

        if (!is_string($class) || !class_exists($class) && !interface_exists($class)) {
            return null;
        }

        $potentialClassFactoryInterfaces = class_implements($class);

        foreach ($potentialClassFactoryInterfaces as $potentialClassFactoryInterface) {
            if ($potentialClassFactoryInterface === CustomFactoryInterface::class) {
                return $class;
            }
        }

        return null;
    }

    public static function getInstance(): Container
    {
        if (!isset(Container::$instance)) {
            throw new ContainerNotInitialisedException('Container not initialised');
        }

        return Container::$instance;
    }

    public function isValue(string $id): bool
    {
        return isset($this->config[$id]) && is_object($this->config[$id]);
    }

    public function getValue(string $id): object
    {
        return $this->config[$id];
    }

    public function addToConfig(array $config = []): void
    {
        $this->config += $config;
    }
}
