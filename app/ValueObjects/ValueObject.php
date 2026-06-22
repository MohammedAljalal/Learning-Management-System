<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;
use Stringable;

/**
 * Abstract Value Object Base
 *
 * Value Objects are immutable, self-validating domain primitives.
 * They encapsulate validation rules and guarantee that only valid
 * values can exist in the domain.
 *
 * Rules (DDD):
 *  - No identity (not persisted directly as entities).
 *  - Immutable — no setters; use named constructors.
 *  - Equality based on value, not reference.
 *
 * Usage:
 *   // Domain-specific value objects extend this class.
 *   class EmailAddress extends ValueObject { ... }
 */
abstract class ValueObject implements Stringable
{
    /**
     * Validate the provided value on construction.
     * Throw InvalidArgumentException if validation fails.
     *
     * @throws InvalidArgumentException
     */
    abstract protected function validate(): void;

    /**
     * Compare equality with another Value Object of the same type.
     */
    public function equals(self $other): bool
    {
        return $this::class === $other::class
            && (string) $this === (string) $other;
    }
}
