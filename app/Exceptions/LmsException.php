<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Base LMS Domain Exception
 *
 * All domain-specific exceptions MUST extend this class so that they can
 * be caught at a single boundary and handled uniformly.
 *
 * Usage:
 *   throw LmsException::because('Enrollment period has closed.');
 *   throw LmsException::withContext('Quota exceeded.', ['quota' => 50]);
 */
abstract class LmsException extends RuntimeException
{
    /**
     * Human-readable context that is safe to log (no PII).
     *
     * @var array<string, mixed>
     */
    protected readonly array $context;

    /**
     * @param array<string, mixed> $context
     */
    final public function __construct(
        string $message = '',
        array $context = [],
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);

        $this->context = $context;
    }

    /**
     * Construct a domain exception with a message only.
     *
     * @return static
     */
    public static function because(string $reason): static
    {
        return new static($reason);
    }

    /**
     * Construct a domain exception with additional log-safe context.
     *
     * @param  array<string, mixed> $context
     * @return static
     */
    public static function withContext(string $reason, array $context): static
    {
        return new static($reason, $context);
    }

    /**
     * Return log-safe context attached to this exception.
     *
     * SECURITY: Never include passwords, tokens, or PII in context arrays.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return $this->context;
    }
}
