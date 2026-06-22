<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Log\LogLevel;
use Throwable;

/**
 * Global Exception Handler
 *
 * Centralises exception reporting and rendering for the entire application.
 *
 * Security principles applied here:
 *  - Generic error messages are returned to clients (fail safe).
 *  - Detailed stack traces are logged server-side only.
 *  - Sensitive data is never surfaced in error responses.
 */
final class Handler extends ExceptionHandler
{
    /**
     * A list of exception types that should not be reported.
     *
     * Add only exceptions that are expected, non-critical, and produce
     * no actionable information in the logs.
     *
     * @var array<class-string<Throwable>>
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed to the session on validation
     * exceptions. Always include credential fields to prevent exposure.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'token',
        'secret',
    ];

    /**
     * Log level overrides per exception type.
     *
     * @var array<class-string<Throwable>, LogLevel::*>
     */
    protected $levels = [];

    /**
     * Register the exception handling callbacks for the application.
     *
     * Renderable and reportable closures are registered here.
     * Domain-specific exception mappings belong in domain service providers.
     */
    public function register(): void
    {
        // Report all unhandled exceptions with maximum context.
        // SECURITY: Ensure no sensitive data is included in the context.
        $this->reportable(function (Throwable $e): void {
            // Additional reporting (Sentry, Bugsnag, etc.) can be wired here.
        });
    }
}
