<?php

declare(strict_types=1);

namespace Quillstack\DI;

use Quillstack\DI\Exceptions\UnresolvableParameterTypeException;
use Quillstack\DI\InstanceFactories\ClassFromInterfaceFactory;
use Quillstack\DI\InstanceFactories\InstantiableClassFactory;
use ReflectionClass;
use ReflectionException;

/**
 * The main factory, creates an instance of the given class/interface or use a parameter.
 */
final class InstanceFactory implements InstanceFactoryInterface
{
    /**
     * Cache for custom factories.
     *
     * @var array<string, CustomFactoryInterface>
     */
    private array $customFactories = [];

    /**
     * InstanceFactory constructor.
     *
     * @param Container $container
     * @param InstantiableClassFactory  $instantiableClassFactory
     * @param ClassFromInterfaceFactory $classFromInterfaceFactory
     */
    public function __construct(
        private Container $container,
        private InstantiableClassFactory $instantiableClassFactory,
        private ClassFromInterfaceFactory $classFromInterfaceFactory
    ) {
    }

    /**
     * Initialise cache for the custom factory.
     *
     * @param class-string<CustomFactoryInterface> $customFactoryClassName
     */
    public function classFromCustomFactory(string $customFactoryClassName): CustomFactoryInterface
    {
        if (!isset($this->customFactories[$customFactoryClassName])) {
            $this->customFactories[$customFactoryClassName] = new $customFactoryClassName();
        }

        return $this->customFactories[$customFactoryClassName];
    }

    /**
     * Creates a new instance.
     *
     * @param class-string $id
     *
     * @throws ReflectionException
     */
    public function create(string $id): object
    {
        $customFactoryClassName = $this->container->getCustomFactoryClassName($id);

        if ($customFactoryClassName !== null && is_subclass_of($customFactoryClassName, CustomFactoryInterface::class)) {
            return $this->createFromCustomFactory($id, $customFactoryClassName);
        }

        if ($this->container->isValue($id)) {
            return $this->container->getValue($id);
        }

        return $this->createFromReflection($id);
    }

    /**
     * Create a reflection and try to return an object.
     *
     * @param class-string $id
     *
     * @throws ReflectionException|UnresolvableParameterTypeException
     */
    private function createFromReflection(string $id): object
    {
        $class = new ReflectionClass($id);

        if ($class->isInstantiable()) {
            return $this->createInstantiable($id);
        }

        if ($class->isInterface()) {
            return $this->createFromInterface($id);
        }

        throw new UnresolvableParameterTypeException("Parameter type of `{$id}` not known");
    }

    /**
     * Create an instance from the custom factory.
     *
     * @param class-string<CustomFactoryInterface> $customFactoryClassName
     */
    private function createFromCustomFactory(string $id, string $customFactoryClassName): object
    {
        return $this->classFromCustomFactory($customFactoryClassName)
            ->setContainer($this->container)
            ->create($id);
    }

    /**
     * Create an instance of the instantiable class.
     */
    private function createInstantiable(string $id): object
    {
        return $this->instantiableClassFactory
            ->setContainer($this->container)
            ->create($id);
    }

    /**
     * Create an instance from the interface.
     *
     * @param string $id
     *
     * @return object
     */
    private function createFromInterface(string $id): object
    {
        return $this->classFromInterfaceFactory
            ->setContainer($this->container)
            ->create($id);
    }
}
