<?php

declare(strict_types=1);

namespace App\DTOs;

use ReflectionClass;
use ReflectionProperty;

/**
 * Abstract Data Transfer Object (DTO) Base
 *
 * DTOs are read-only value containers used to transfer data between
 * application layers (e.g., from a Request to a Service or Action).
 *
 * Principles:
 *  - Immutable after construction (readonly properties).
 *  - No business logic — carry data only.
 *  - Typed properties prevent untyped data from crossing layer boundaries.
 *
 * Usage:
 *   class CreateCourseDto extends DataTransferObject { ... }
 *   $dto = CreateCourseDto::fromArray($request->validated());
 */
abstract class DataTransferObject
{
    /**
     * Construct a DTO from an associative array.
     * Keys must match constructor parameter names exactly.
     *
     * @param  array<string, mixed> $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $class      = new ReflectionClass(static::class);
        $constructor = $class->getConstructor();

        if ($constructor === null) {
            return new static();
        }

        $args = [];

        foreach ($constructor->getParameters() as $parameter) {
            $name   = $parameter->getName();
            $args[] = $data[$name] ?? ($parameter->isDefaultValueAvailable()
                ? $parameter->getDefaultValue()
                : null
            );
        }

        return new static(...$args);
    }

    /**
     * Serialise the DTO to an associative array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $result     = [];

        foreach ($properties as $property) {
            $result[$property->getName()] = $property->getValue($this);
        }

        return $result;
    }
}
