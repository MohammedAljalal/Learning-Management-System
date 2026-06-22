<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Set Security Headers Middleware
 *
 * Applies a hardened set of HTTP security headers on every response.
 * This is the primary defence-in-depth layer at the HTTP level.
 *
 * Security headers enforced:
 *  - Content-Security-Policy   : Restricts resource loading origins.
 *  - X-Frame-Options           : Prevents clickjacking (legacy).
 *  - X-Content-Type-Options    : Prevents MIME sniffing.
 *  - Referrer-Policy           : Controls referrer information leakage.
 *  - Permissions-Policy        : Disables unused browser APIs.
 *  - Strict-Transport-Security : Enforces HTTPS (production only).
 *  - X-XSS-Protection          : Legacy XSS filter for older browsers.
 *
 * SECURITY NOTES:
 *  - CSP nonce-based inline script support is handled by Livewire
 *    natively via @nonce in Blade. Adjust script-src accordingly.
 *  - HSTS is only set in production to avoid breaking local HTTPS-less dev.
 *  - Security enhancements for SSO and MFA should be implemented in future phases.
 */
final class SetSecurityHeaders
{
    /**
     * Handle an incoming request and append security headers to the response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $this->applyHeaders($response, $request);

        return $response;
    }

    /**
     * Apply all security headers to the response.
     */
    private function applyHeaders(Response $response, Request $request): void
    {
        // ── Content Security Policy ──────────────────────────────────────────
        // MUST be adjusted as new resource origins (fonts, analytics, etc.)
        // are added. Start restrictive; open up only what is needed.
        $nonce = $this->nonce();
        $devUrls = app()->isLocal() ? 'http://127.0.0.1:5173 http://localhost:5173' : '';
        
        $scriptSrc = $nonce
            ? "script-src 'self' 'unsafe-eval' 'nonce-{$nonce}' $devUrls"
            : "script-src 'self' 'unsafe-inline' 'unsafe-eval' $devUrls";
        $styleSrc = $nonce
            ? "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com 'nonce-{$nonce}' $devUrls"
            : "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com $devUrls";

        $response->headers->set(
            'Content-Security-Policy',
            implode('; ', array_filter([
                "default-src 'self'",
                $scriptSrc,
                $styleSrc,
                "img-src 'self' data: blob:",
                "font-src 'self' data: https://fonts.gstatic.com https://fonts.bunny.net",
                "connect-src 'self' ws: wss: blob: https://fonts.googleapis.com",
                "media-src 'self' blob:",
                "frame-src 'none'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'none'",
                app()->isProduction() ? "upgrade-insecure-requests" : null,
            ]))
        );

        // ── Clickjacking Protection (legacy) ─────────────────────────────────
        $response->headers->set('X-Frame-Options', 'DENY');

        // ── MIME Sniffing Prevention ──────────────────────────────────────────
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // ── Referrer Leakage Control ──────────────────────────────────────────
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ── Disable Unused Browser APIs ───────────────────────────────────────
        $response->headers->set(
            'Permissions-Policy',
            implode(', ', [
                'camera=()',
                'microphone=()',
                'geolocation=()',
                'payment=()',
                'usb=()',
                'magnetometer=()',
                'gyroscope=()',
                'accelerometer=()',
            ])
        );

        // ── Legacy XSS Filter (IE / older Chrome) ─────────────────────────────
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // ── HSTS – Production only (HTTPS required) ───────────────────────────
        // max-age = 1 year; includeSubDomains and preload for HSTS preload list.
        if (app()->isProduction()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // ── Remove Server fingerprint headers ─────────────────────────────────
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
    }

    /**
     * Return the CSP nonce for the current request.
     *
     * Livewire generates its own nonce; we reuse it here so inline
     * Livewire scripts are allowed by the CSP without 'unsafe-inline'.
     */
    private function nonce(): string
    {
        return app()->has('csp-nonce')
            ? (string) app('csp-nonce')
            : '';
    }
}
