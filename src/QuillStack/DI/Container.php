<?php

declare(strict_types=1);

namespace QuillStack\DI;

use Psr\Container\ContainerInterface;
use QuillStack\DI\Exceptions\ContainerNotInitialisedException;
use QuillStack\DI\Exceptions\IncorrectClassTypeException;
use QuillStack\DI\Exceptions\ClassNotFoundForInterfaceException;
use QuillStack\DI\Exceptions\InterfaceDefinitionNotFoundException;
use QuillStack\DI\Exceptions\ParameterDefinitionNotFoundException;
use QuillStack\DI\Exceptions\UnableToCreateReflectionClassException;
use ReflectionException;

/**
 * The Dependency Injection Container.
 */
final class Container implements ContainerInterface
{
    /**
     * Instances array.
     *
     * @var array
     */
    private array $instances;

    /**
     * Instance factory to create new instances.
     *
     * @var InstanceFactory
     */
    private InstanceFactory $instanceFactory;

    /**
     * Configuration for interfaces and parameters.
     *
     * @var array
     */
    private array $config;

    /**
     * @var Container
     */
    private static Container $instance;

    /**
     * Container constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->instanceFactory = new InstanceFactory($this);
        static::$instance = $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
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
     *
     * @param $id
     */
    private function createNewInstance($id): void
    {
        try {
            $this->instances[$id] = $this->instanceFactory->create($id);
        } catch (ReflectionException $exception) {
            $message = "Unable to create reflection class for `{$id}`";

            throw new UnableToCreateReflectionClassException($message, 500, $exception);
        }

        static::$instance = $this;
    }

    /**
     * Gets a class name for the given interface from the configuration.
     *
     * @param string $interface
     *
     * @return mixed
     */
    public function getInstantiableClassForInterface(string $interface)
    {
        if (!isset($this->config[$interface])) {
            throw new InterfaceDefinitionNotFoundException("Interface definition `{$interface}` not found");
        }

        $class = $this->config[$interface];

        if (!is_string($class)) {
            throw new IncorrectClassTypeException("Incorrect class type for interface `{$interface}`");
        }

        if (!class_exists($class)) {
            throw new ClassNotFoundForInterfaceException("Class not found for interface `{$interface}`");
        }

        return $class;
    }

    /**
     * Gets a parameter value for the given name from the configuration.
     *
     * @param string $className
     * @param string $parameterName
     *
     * @return mixed
     */
    public function getParameterForClass(string $className, string $parameterName)
    {
        if (!isset($this->config[$className][$parameterName])) {
            return null;
        }

        return $this->config[$className][$parameterName];
    }

    /**
     * @param string $classNameOrInterface
     *
     * @return string|null
     */
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

    /**
     * @param string $id
     *
     * @return string|null
     */
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

    /**
     * @return Container
     */
    public static function getInstance(): Container
    {
        if (!isset(static::$instance)) {
            throw new ContainerNotInitialisedException('Container not initilised');
        }

        return static::$instance;
    }
}
