<?php

use App\Http\Middleware\SetSecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /*
        |----------------------------------------------------------------------
        | Global HTTP Middleware
        |----------------------------------------------------------------------
        |
        | These middleware run on every HTTP request to the application.
        |
        | Security headers are applied first so every response — including
        | error pages — is protected.
        |
        */
        $middleware->append(SetSecurityHeaders::class);

        /*
        |----------------------------------------------------------------------
        | CSRF Protection
        |----------------------------------------------------------------------
        |
        | Laravel's built-in CSRF middleware is enabled by default on all
        | web routes. MUST NOT be disabled.
        |
        | TODO(security): Exempt only verified webhook endpoints (Stripe, etc.)
        | using $middleware->validateCsrfTokens(except: [...]).
        |
        */

        /*
        |----------------------------------------------------------------------
        | Trusted Proxies
        |----------------------------------------------------------------------
        |
        | Configure trusted proxies for deployments behind load balancers.
        | Set TRUSTED_PROXIES in .env for production.
        |
        */
        $middleware->trustProxies(
            at: env('TRUSTED_PROXIES', '127.0.0.1'),
        );

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /*
        |----------------------------------------------------------------------
        | Exception Rendering
        |----------------------------------------------------------------------
        |
        | SECURITY: Generic messages are returned to clients.
        | Detailed context is logged server-side only.
        |
        | Domain-specific exception handlers are registered in their
        | respective service providers, not here.
        |
        */
        $exceptions->render(function (\App\Exceptions\LmsException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('app.error'),
                    'code'    => $e->getCode(),
                ], 422);
            }

            return null; // Fall through to default Laravel handling.
        });
    })
    ->create();
