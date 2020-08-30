<?php

declare(strict_types=1);

namespace QuillDI\InstanceFactories;

use QuillDI\Container;
use QuillDI\InstanceFactoryInterface;
use ReflectionClass;

/**
 * The factory for classes.
 */
final class InstantiableClassFactory implements InstanceFactoryInterface
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
     * {@inheritdoc}
     */
    public function create(string $id)
    {
        $constructor = $this->class->getConstructor();

        if ($constructor === null) {
            return new $id();
        }

        $parameters = $constructor->getParameters();

        return $this->createInstanceWithParameters($id, $parameters);
    }

    /**
     * Creates the instance of the class and creates the instances from the parameters, if it's required.
     *
     * @param string $id
     * @param array  $parameters
     *
     * @return mixed
     */
    private function createInstanceWithParameters(string $id, array $parameters)
    {
        foreach ($parameters as $index => $parameter) {
            $parameterClassName = $parameter->getType()->getName();
            $parameterName = $parameter->getName();

            $parameters[$index] = $this->createParameter($parameterClassName, $parameterName);
        }

        return new $id(...$parameters);
    }

    /**
     * Create the instance of one parameter, if it's a class or an interface. For other parameters we try to find
     * the definition of this parameter in the Container.
     *
     * @param string $parameterClassName
     * @param string $parameterName
     *
     * @return mixed
     */
    private function createParameter(string $parameterClassName, string $parameterName)
    {
        if (class_exists($parameterClassName) || interface_exists($parameterClassName)) {
            return $this->container->get($parameterClassName);
        }

        return $this->container->getParameterForClass(
            $this->class->getName(),
            $parameterName
        );
    }
}
