<?php

declare(strict_types=1);

namespace Quillstack\DI\Definitions;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Says how a class is built: what its constructor asks for, and which of its public
 * properties the container fills. Reflection answers that once per class.
 */
final class Definitions
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $definitions = [];

    /**
     * @param class-string $className
     *
     * @return array{parameters: ?array<int, array<string, mixed>>, properties: array<int, array<string, mixed>>}
     *
     * @throws ReflectionException
     */
    public function get(string $className): array
    {
        if (isset($this->definitions[$className])) {
            /** @var array{parameters: ?array<int, array<string, mixed>>, properties: array<int, array<string, mixed>>} $known */
            $known = $this->definitions[$className];

            return $known;
        }

        $definition = $this->describe(new ReflectionClass($className));
        $this->definitions[$className] = $definition;

        return $definition;
    }

    /**
     * @param ReflectionClass<object> $class
     *
     * @return array{parameters: ?array<int, array<string, mixed>>, properties: array<int, array<string, mixed>>}
     */
    private function describe(ReflectionClass $class): array
    {
        $constructor = $class->getConstructor();
        $defaults = $class->getDefaultProperties();

        return [
            'parameters' => $constructor === null
                ? null
                : array_map(fn (ReflectionParameter $p): array => $this->describeParameter($p), $constructor->getParameters()),
            'properties' => $this->describeProperties($class, $defaults),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function describeParameter(ReflectionParameter $parameter): array
    {
        return [
            'name' => $parameter->getName(),
            'class' => $this->classNameOf($parameter->getType()),
            'optional' => $parameter->isOptional(),
            'default' => $parameter->isOptional() && $parameter->isDefaultValueAvailable()
                ? $parameter->getDefaultValue()
                : null,
        ];
    }

    /**
     * Only public properties are filled, and only those the constructor did not already
     * fill: a promoted one was asked for there, and a readonly one cannot be written twice.
     *
     * @param ReflectionClass<object> $class
     * @param array<string, mixed> $defaults
     *
     * @return array<int, array<string, mixed>>
     */
    private function describeProperties(ReflectionClass $class, array $defaults): array
    {
        $properties = [];

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $type = $property->getType();

            if (!$type instanceof ReflectionNamedType || $property->isPromoted() || $property->isReadOnly()) {
                continue;
            }

            $name = $property->getName();
            $properties[] = [
                'name' => $name,
                'class' => $this->classNameOf($type),
                'default' => $defaults[$name] ?? null,
                'nullable' => $type->allowsNull(),
            ];
        }

        return $properties;
    }

    /**
     * The class a type names, when it names exactly one that exists. A union, an
     * intersection or a built-in type names none.
     */
    private function classNameOf(mixed $type): ?string
    {
        if (!$type instanceof ReflectionNamedType) {
            return null;
        }

        $name = $type->getName();

        return class_exists($name) || interface_exists($name) ? $name : null;
    }
}
