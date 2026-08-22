<?php

declare(strict_types=1);

namespace Quillstack\DI;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Quillstack\DI\Definitions\Definitions;
use Quillstack\DI\Exceptions\ClassLoopException;
use Quillstack\DI\Exceptions\ClassNotFoundForInterfaceException;
use Quillstack\DI\Exceptions\IncorrectClassTypeException;
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
     *
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * Current classes stack to detect loops.
     *
     * @var string[]
     */
    private array $stack = [];

    /**
     * Instance factory to create new instances.
     */
    private InstanceFactory $instanceFactory;

    /**
     * Configuration for interfaces and parameters.
     *
     * @var array<string, mixed>
     */
    private array $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->instanceFactory = new InstanceFactory(
            $this,
            new InstantiableClassFactory(new Definitions()),
            new ClassFromInterfaceFactory()
        );

    }

    /**
     * {@inheritdoc}
     */
    public function get(string $id): mixed
    {
        // Asking the container for a container, by either name, gives this one back.
        if ($id === Container::class || $id === ContainerInterface::class) {
            return $this;
        }

        if (!isset($this->instances[$id])) {
            $this->createNewInstance($id);
        }

        return $this->instances[$id];
    }

    /**
     * {@inheritdoc}
     *
     * Returns `true` when `get($id)` is able to resolve the entry, as required by PSR-11.
     * Autowiring has no explicit definitions to inspect, so an entry which is neither cached
     * nor configured is resolved once, and the result is kept for the following `get()` call.
     */
    public function has(string $id): bool
    {
        if ($id === Container::class || $id === ContainerInterface::class) {
            return true;
        }

        if (isset($this->instances[$id]) || isset($this->config[$id])) {
            return true;
        }

        try {
            $this->get($id);
        } catch (NotFoundExceptionInterface) {
            return false;
        } catch (ContainerExceptionInterface) {
            // The entry is known, but could not be built for an unrelated reason.
            return true;
        }

        return true;
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
            /** @var class-string $id */
            $this->instances[$id] = $this->instanceFactory->create($id);
        } catch (ReflectionException $exception) {
            $message = "Unable to create reflection class for `{$id}`";

            throw new UnableToCreateReflectionClassException($message, 500, $exception);
        } finally {
            // However this ended, the class is no longer being built. Leaving it on the
            // stack would have the next attempt at the same one reported as a loop.
            array_pop($this->stack);
        }
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
        $parameters = $this->config[$className] ?? null;

        if (!is_array($parameters)) {
            return null;
        }

        return $parameters[$parameterName] ?? null;
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

    public function isValue(string $id): bool
    {
        return isset($this->config[$id]) && is_object($this->config[$id]);
    }

    public function getValue(string $id): object
    {
        /** @var object $value */
        $value = $this->config[$id];

        return $value;
    }

    /**
     * @param array<string, mixed> $config
     */
    public function addToConfig(array $config = []): void
    {
        $this->config += $config;
    }

    /**
     * The configuration this container was built with, plus anything added later. A test
     * runner uses it to build a fresh container holding the same definitions.
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
