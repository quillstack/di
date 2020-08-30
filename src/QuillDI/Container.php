<?php

declare(strict_types=1);

namespace QuillDI;

use Psr\Container\ContainerInterface;
use QuillDI\Exceptions\ContainerException;
use QuillDI\Exceptions\InterfaceDefinitionNotFoundException;
use QuillDI\Exceptions\ParameterDefinitionNotFoundException;
use QuillDI\Exceptions\UnableToCreateReflectionClassException;
use ReflectionException;
use Throwable;

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
     * Configuration for interfaces.
     *
     * @var array
     */
    private array $interfaces;

    /**
     * Parameters for instances.
     *
     * @var array
     */
    private array $parameters;

    /**
     * Container constructor.
     *
     * @param array $interfaces
     * @param array $parameters
     */
    public function __construct(array $interfaces = [], array $parameters = [])
    {
        $this->interfaces = $interfaces;
        $this->parameters = $parameters;
        $this->instanceFactory = new InstanceFactory($this);
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        if (!isset($this->instances[$id])) {
            $this->createNewInstance($id);
        }

        return $this->instances[$id];
    }

    /**
     * {@inheritDoc}
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
        } catch (ContainerException $exception) {
            throw $exception;
        } catch (ReflectionException $exception) {
            $message = "Unable to create reflection class for `{$id}`";
            throw new UnableToCreateReflectionClassException($message, 500 ,$exception);
        }
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
        if (!isset($this->interfaces[$interface])) {
            throw new InterfaceDefinitionNotFoundException("Interface definition `{$interface}` not found");
        }

        return $this->interfaces[$interface];
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
        if (!isset($this->parameters[$className][$parameterName])) {
            $message = "Parameter definition `{$parameterName}` for class `{$className}` not found";

            throw new ParameterDefinitionNotFoundException($message);
        }

        return $this->parameters[$className][$parameterName];
    }
}
