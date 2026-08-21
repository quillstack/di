<?php

declare(strict_types=1);

namespace Quillstack\DI\InstanceFactories;

use Quillstack\DI\Container;
use Quillstack\DI\Definitions\Definitions;
use Quillstack\DI\Exceptions\ParameterDefinitionNotFoundException;
use Quillstack\DI\InstanceFactoryWithContainerInterface;
use TypeError;

/**
 * Builds a class from the description of it: what the constructor asks for, and which public
 * properties are filled afterwards. Working that out is the job of Definitions, which reads
 * it once and remembers it.
 */
class InstantiableClassFactory implements InstanceFactoryWithContainerInterface
{
    private Container $container;

    public function __construct(private readonly Definitions $definitions)
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container): self
    {
        $this->container = $container;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $id): object
    {
        /** @var class-string $id */
        $definition = $this->definitions->get($id);
        $object = $this->build($id, $definition['parameters']);

        return $this->fillProperties($object, $id, $definition['properties']);
    }

    /**
     * @param ?array<int, array<string, mixed>> $parameters
     */
    private function build(string $id, ?array $parameters): object
    {
        if ($parameters === null) {
            return new $id();
        }

        $arguments = array_map(
            fn (array $parameter): mixed => $this->readParameter($id, $parameter),
            $parameters
        );

        try {
            return new $id(...$arguments);
        } catch (TypeError $exception) {
            throw new ParameterDefinitionNotFoundException($exception->getMessage());
        }
    }

    /**
     * A parameter naming a class is built by the container. Anything else is looked up in
     * the configuration, and falls back to the default the constructor declares.
     *
     * @param array<string, mixed> $parameter
     */
    private function readParameter(string $className, array $parameter): mixed
    {
        if (is_string($parameter['class'])) {
            return $this->container->get($parameter['class']);
        }

        /** @var string $name */
        $name = $parameter['name'];
        $value = $this->container->getParameterForClass($className, $name);

        if ($value !== null) {
            return $value;
        }

        return $parameter['optional'] === true ? $parameter['default'] : null;
    }

    /**
     * @param array<int, array<string, mixed>> $properties
     */
    private function fillProperties(object $object, string $className, array $properties): object
    {
        foreach ($properties as $property) {
            /** @var string $name */
            $name = $property['name'];
            $valueFromConfig = $this->container->getParameterForClass($className, $name);

            if (is_string($property['class'])) {
                $object->$name = $this->container->get($property['class']);
            } elseif (isset($valueFromConfig)) {
                $object->$name = $valueFromConfig;
            } elseif ($property['default']) {
                $object->$name = $property['default'];
            } elseif ($property['nullable'] === true) {
                $object->$name = null;
            }
        }

        return $object;
    }
}
