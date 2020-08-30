<?php

declare(strict_types=1);

namespace QuillDI;

use QuillDI\Exceptions\UnresolvableParameterTypeException;
use QuillDI\InstanceFactories\ClassFromInterfaceFactory;
use QuillDI\InstanceFactories\InstantiableClassFactory;
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
    public static function instantiableClassFactory()
    {
        if (!static::$instantiableClassFactory) {
            static::$instantiableClassFactory = new InstantiableClassFactory();
        }

        return static::$instantiableClassFactory;
    }

    /**
     * Initialise cache for the factory instance to create objects from interface.
     *
     * @return ClassFromInterfaceFactory|null
     */
    public static function classFromInterfaceFactory()
    {
        if (!static::$classFromInterfaceFactory) {
            static::$classFromInterfaceFactory = new ClassFromInterfaceFactory();
        }

        return static::$classFromInterfaceFactory;
    }

    /**
     * Creates a new instance.
     *
     * @param string $id
     *
     * @return mixed
     *
     * @throws ReflectionException
     */
    public function create(string $id)
    {
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
     * Create an instance of the instantiable class.
     *
     * @param string $id
     * @param ReflectionClass $class
     *
     * @return mixed
     */
    private function createInstantiable(string $id, ReflectionClass $class)
    {
        return static::instantiableClassFactory()
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
        return static::classFromInterfaceFactory()
            ->setContainer($this->container)
            ->create($id);
    }
}
