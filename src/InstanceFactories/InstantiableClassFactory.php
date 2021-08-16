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
    private function createObjectWithParameters(string $id, ReflectionMethod $constructor = null): object
    {
        if ($constructor === null) {
            return new $id();
        }

        // If we know here, that this object requires parameters, let's find them and return them later.
        $parameters = $constructor->getParameters();

        return $this->createInstanceWithParameters($id, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $id): object
    {
        $constructor = $this->class->getConstructor();
        $object = $this->createObjectWithParameters($id, $constructor);
        $properties = $this->getProperties($object);

        return $this->createInstanceWithProperties($object, $properties);
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
    private function createInstanceWithProperties($object, array $properties): object
    {
        foreach ($properties['properties'] as $property) {
            $name = $property->getName();
            $type = $property->getType() ? $property->getType()->getName() : null;
            $value = $properties['defaults'][$name] ?? null;

            if (!$type) {
                continue;
            }

            $valueFromConfig = $this->createParameterFromConfig($name);

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
    private function createInstanceWithParameters(string $id, array $parameters): object
    {
        foreach ($parameters as $index => $parameter) {
            $parameters[$index] = $this->createParameter($parameter);
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
    private function createParameter($parameter): mixed
    {
        $parameterType = $parameter->getType();

        if (!$parameterType) {
            return $this->createFromConfigOrGetDefault($parameter);
        }

        $parameterClassName = $parameterType->getName();

        if (class_exists($parameterClassName) || interface_exists($parameterClassName)) {
            return $this->container->get($parameterClassName);
        }

        return $this->createFromConfigOrGetDefault($parameter);
    }

    /**
     * @param $parameter
     *
     * @return mixed
     */
    private function createDefaultIfOptional($parameter): mixed
    {
        return $parameter->isOptional()
            ? $parameter->getDefaultValue()
            : $this->createParameterFromConfig($parameter->getName());
    }

    /**
     * @param $parameter
     *
     * @return mixed
     */
    private function createFromConfigOrGetDefault($parameter): mixed
    {
        $parameterName = $parameter->getName();
        $value = $this->createParameterFromConfig($parameterName);

        if ($value !== null) {
            return $value;
        }

        return $this->createDefaultIfOptional($parameter);
    }

    /**
     * @param string $parameterName
     *
     * @return mixed
     */
    private function createParameterFromConfig(string $parameterName): mixed
    {
        return $this->container->getParameterForClass(
            $this->class->getName(),
            $parameterName
        );
    }
}
