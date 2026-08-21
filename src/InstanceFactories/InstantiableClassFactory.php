<?php

declare(strict_types=1);

namespace Quillstack\DI\InstanceFactories;

use JetBrains\PhpStorm\ArrayShape;
use Quillstack\DI\Container;
use Quillstack\DI\Exceptions\ParameterDefinitionNotFoundException;
use Quillstack\DI\InstanceFactoryWithContainerInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use TypeError;

/**
 * The factory for classes.
 */
class InstantiableClassFactory implements InstanceFactoryWithContainerInterface
{
    /**
     * The instance of the Reflection class to find out all parameters we need to create before we initialise
     * the instance of the given class.
     *
     * @var ReflectionClass
     */
    private ReflectionClass $class;

    /**
     * The instance of the Container class.
     *
     * @var Container
     */
    private Container $container;

    /**
     * Sets the instance of the Reflection class.
     *
     * @param ReflectionClass $class
     *
     * @return InstantiableClassFactory
     */
    public function setClass(ReflectionClass $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Sets the instance of the Container class.
     *
     * @param Container $container
     *
     * @return InstantiableClassFactory
     */
    public function setContainer(Container $container): self
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Create object with parameters to find properties later.
     *
     * @param string $id
     * @param ReflectionMethod|null $constructor
     *
     * @return object
     */
    private function createObjectWithParameters(
        string $id,
        string $className,
        ?ReflectionMethod $constructor = null
    ): object {
        if ($constructor === null) {
            return new $id();
        }

        // If we know here, that this object requires parameters, let's find them and return them later.
        $parameters = $constructor->getParameters();

        return $this->createInstanceWithParameters($id, $className, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $id): object
    {
        // Building a dependency runs through this same factory and sets a class of its
        // own, so the one being built here is read once and carried along.
        $class = $this->class;
        $className = $class->getName();

        $object = $this->createObjectWithParameters($id, $className, $class->getConstructor());
        $properties = $this->getProperties($object);

        return $this->createInstanceWithProperties($object, $className, $properties);
    }

    /**
     * Finds only public properties with default values.
     *
     * @param $object
     *
     * @return array
     */
    #[ArrayShape(['properties' => "\ReflectionProperty[]", 'defaults' => "mixed[]"])]
    private function getProperties($object): array
    {
        $reflect = new ReflectionClass($object);

        return [
            'properties' => $reflect->getProperties(ReflectionProperty::IS_PUBLIC),
            'defaults' => $reflect->getDefaultProperties(),
        ];
    }

    /**
     * Creates the instance of the class with properties.
     *
     * @param $object
     * @param array $properties
     *
     * @return object
     */
    private function createInstanceWithProperties($object, string $className, array $properties): object
    {
        foreach ($properties['properties'] as $property) {
            $name = $property->getName();
            $type = $property->getType() ? $property->getType()->getName() : null;
            $value = $properties['defaults'][$name] ?? null;

            if (!$type) {
                continue;
            }

            // A promoted property was asked for through the constructor, where the
            // configuration was already read, and a readonly one cannot be written twice.
            if ($property->isPromoted() || $property->isReadOnly()) {
                continue;
            }

            $valueFromConfig = $this->createParameterFromConfig($className, $name);

            if (class_exists($type) || interface_exists($type)) {
                $object->$name = $this->container->get($type);
            } elseif (isset($valueFromConfig)) {
                $object->$name = $valueFromConfig;
            } elseif ($value) {
                $object->$name = $value;
            } elseif ($property->getType()->allowsNull()) {
                $object->$name = null;
            }
        }

        return $object;
    }

    /**
     * Creates the instance of the class and creates the instances from the parameters, if it's required.
     *
     * @param string $id
     * @param array $parameters
     *
     * @return object
     */
    private function createInstanceWithParameters(string $id, string $className, array $parameters): object
    {
        foreach ($parameters as $index => $parameter) {
            $parameters[$index] = $this->createParameter($className, $parameter);
        }

        try {
            return new $id(...$parameters);
        } catch (TypeError $exception) {
            throw new ParameterDefinitionNotFoundException(
                $exception->getMessage()
            );
        }
    }

    /**
     * Create the instance of one parameter, if it's a class or an interface. For other parameters we try to find
     * the definition of this parameter in the Container.
     *
     * @param $parameter
     *
     * @return mixed
     */
    private function createParameter(string $className, $parameter): mixed
    {
        $parameterType = $parameter->getType();

        if (!$parameterType) {
            return $this->createFromConfigOrGetDefault($className, $parameter);
        }

        $parameterClassName = $parameterType->getName();

        if (class_exists($parameterClassName) || interface_exists($parameterClassName)) {
            return $this->container->get($parameterClassName);
        }

        return $this->createFromConfigOrGetDefault($className, $parameter);
    }

    /**
     * @param $parameter
     *
     * @return mixed
     */
    private function createDefaultIfOptional(string $className, $parameter): mixed
    {
        return $parameter->isOptional()
            ? $parameter->getDefaultValue()
            : $this->createParameterFromConfig($className, $parameter->getName());
    }

    /**
     * @param $parameter
     *
     * @return mixed
     */
    private function createFromConfigOrGetDefault(string $className, $parameter): mixed
    {
        $parameterName = $parameter->getName();
        $value = $this->createParameterFromConfig($className, $parameterName);

        if ($value !== null) {
            return $value;
        }

        return $this->createDefaultIfOptional($className, $parameter);
    }

    /**
     * @param string $parameterName
     *
     * @return mixed
     */
    private function createParameterFromConfig(string $className, string $parameterName): mixed
    {
        return $this->container->getParameterForClass($className, $parameterName);
    }
}
