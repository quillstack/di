<?php

declare(strict_types=1);

namespace QuillStack\DI;

use QuillStack\DI\Exceptions\UnresolvableParameterTypeException;
use QuillStack\DI\InstanceFactories\ClassFromInterfaceFactory;
use QuillStack\DI\InstanceFactories\InstantiableClassFactory;
use ReflectionClass;
use ReflectionException;

/**
 * The main factory, creates an instance of the given class/interface or use a parameter.
 */
final class InstanceFactory implements InstanceFactoryInterface
{
    /**
     * Cache for the factory instance to create objects from interface.
     *
     * @var ClassFromInterfaceFactory|null
     */
    private static ?ClassFromInterfaceFactory $classFromInterfaceFactory = null;

    /**
     * Cache for the factory instance to instances of instantiable classes.
     *
     * @var InstantiableClassFactory|null
     */
    private static ?InstantiableClassFactory $instantiableClassFactory = null;

    /**
     * Cache for custom factories.
     *
     * @var array
     */
    private static array $customFactories = [];

    /**
     * The container instance.
     *
     * @var Container
     */
    private Container $container;

    /**
     * InstanceFactory constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Initialise cache for the factory instance to instances of instantiable classes.
     *
     * @return InstantiableClassFactory|null
     */
    public static function instantiableClassFactory(): ?InstantiableClassFactory
    {
        if (!self::$instantiableClassFactory) {
            self::$instantiableClassFactory = new InstantiableClassFactory();
        }

        return self::$instantiableClassFactory;
    }

    /**
     * Initialise cache for the factory instance to create objects from interface.
     *
     * @return ClassFromInterfaceFactory|null
     */
    public static function classFromInterfaceFactory(): ?ClassFromInterfaceFactory
    {
        if (!self::$classFromInterfaceFactory) {
            self::$classFromInterfaceFactory = new ClassFromInterfaceFactory();
        }

        return self::$classFromInterfaceFactory;
    }

    /**
     * Initialise cache for the custom factory.
     *
     * @param string $customFactoryClassName
     *
     * @return CustomFactoryInterface|null
     */
    public static function classFromCustomFactory(string $customFactoryClassName): ?CustomFactoryInterface
    {
        if (!isset(self::$customFactories[$customFactoryClassName])) {
            self::$customFactories[$customFactoryClassName] = new $customFactoryClassName();
        }

        return self::$customFactories[$customFactoryClassName];
    }

    /**
     * Creates a new instance.
     *
     * @param string $id
     *
     * @throws ReflectionException
     * @return mixed
     */
    public function create(string $id)
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
     * @return mixed
     */
    private function createFromCustomFactory(string $id, string $customFactoryClassName)
    {
        return self::classFromCustomFactory($customFactoryClassName)
            ->setContainer($this->container)
            ->create($id);
    }

    /**
     * Create an instance of the instantiable class.
     *
     * @param string $id
     * @param ReflectionClass $class
     *
     * @return mixed
     * @throws ReflectionException
     */
    private function createInstantiable(string $id, ReflectionClass $class)
    {
        return self::instantiableClassFactory()
            ->setContainer($this->container)
            ->setClass($class)
            ->create($id);
    }

    /**
     * Create an instance from the interface.
     *
     * @param string $id
     *
     * @return mixed
     */
    private function createFromInterface(string $id)
    {
        return self::classFromInterfaceFactory()
            ->setContainer($this->container)
            ->create($id);
    }
}
