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
     * @var array
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
     * @param string $customFactoryClassName
     *
     * @return CustomFactoryInterface|null
     */
    public function classFromCustomFactory(string $customFactoryClassName): ?CustomFactoryInterface
    {
        if (!isset($this->customFactories[$customFactoryClassName])) {
            $this->customFactories[$customFactoryClassName] = new $customFactoryClassName();
        }

        return $this->customFactories[$customFactoryClassName];
    }

    /**
     * Creates a new instance.
     *
     * @param string $id
     *
     * @return object
     * @throws ReflectionException
     */
    public function create(string $id): object
    {
        if ($customFactoryClassName = $this->container->getCustomFactoryClassName($id)) {
            return $this->createFromCustomFactory($id, $customFactoryClassName);
        }

        $class = new ReflectionClass($id);

        if ($class->isInstantiable()) {
            return $this->createInstantiable($id, $class);
        }

        if ($class->isInterface()) {
            return $this->createFromInterface($id);
        }

        throw new UnresolvableParameterTypeException("Parameter type of `{$id}` not known");
    }

    /**
     * Create an instance from the custom factory.
     *
     * @param string $id
     * @param string $customFactoryClassName
     *
     * @return object
     */
    private function createFromCustomFactory(string $id, string $customFactoryClassName): object
    {
        return $this->classFromCustomFactory($customFactoryClassName)
            ->setContainer($this->container)
            ->create($id);
    }

    /**
     * Create an instance of the instantiable class.
     *
     * @param string          $id
     * @param ReflectionClass $class
     *
     * @return object
     */
    private function createInstantiable(string $id, ReflectionClass $class): object
    {
        return $this->instantiableClassFactory
            ->setContainer($this->container)
            ->setClass($class)
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
