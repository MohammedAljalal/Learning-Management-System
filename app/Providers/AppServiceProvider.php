<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Services\AI\AiServiceInterface;
use App\Services\AI\MockAiService;

/**
 * Application Service Provider
 *
 * Registers only globally required infrastructure concerns.
 * Business-domain logic MUST be registered in domain-specific providers.
 *
 * Follows SOLID / SRP: each concern is isolated to its own method.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * Bind interfaces to concrete implementations that are needed
     * application-wide. Do NOT register business logic here.
     */
    public function register(): void
    {
        $this->app->bind(AiServiceInterface::class, \App\Services\AI\GeminiAiService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureModels();
        $this->configureUrl();
        $this->configureJsonResources();
        $this->configureQueryLogging();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private bootstrap helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Configure global Eloquent model behaviour.
     *
     * - Strict mode prevents accidental mass-assignment, lazy-loading, and
     *   accessing non-existent attributes – all of which hide bugs in prod.
     * - In production, strict mode issues log warnings rather than exceptions
     *   to avoid hard failures on existing data issues; exceptions are used
     *   during local development for immediate feedback.
     */
    private function configureModels(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());

        // Prevent lazy loading in production to avoid N+1 query issues
        // that degrade performance at scale.
        Model::preventLazyLoading(! $this->app->isProduction());

        // Silently handle lazy-loading violations in production with a log
        // entry instead of throwing an exception (fail gracefully).
        if ($this->app->isProduction()) {
            Model::handleLazyLoadingViolationUsing(
                static function (Model $model, string $relation): void {
                    // SECURITY: Do not log user-identifiable data here.
                    Log::warning('Eloquent lazy-loading violation detected.', [
                        'model'    => $model::class,
                        'relation' => $relation,
                    ]);
                }
            );
        }
    }

    /**
     * Force HTTPS scheme for generated URLs when running behind a proxy
     * or in a production environment.
     */
    private function configureUrl(): void
    {
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }

    /**
     * Remove the top-level "data" wrapping from JSON API resources.
     *
     * API consumers receive a flat, predictable response structure.
     * Re-enable wrapping if you adopt JSON:API specification.
     */
    private function configureJsonResources(): void
    {
        JsonResource::withoutWrapping();
    }

    /**
     * Log slow database queries in non-production environments.
     *
     * Queries exceeding the threshold are written to the "query" log channel
     * so developers can identify N+1 and missing-index problems early.
     *
     * SECURITY: Query bindings are NOT logged to avoid leaking sensitive data.
     */
    private function configureQueryLogging(): void
    {
        if ($this->app->isProduction()) {
            return;
        }

        /** @var int $thresholdMs Maximum acceptable query time in milliseconds */
        $thresholdMs = (int) env('LOG_SLOW_QUERY_MS', 500);

        DB::listen(static function (object $query) use ($thresholdMs): void {
            if ($query->time >= $thresholdMs) {
                // SECURITY: Bindings omitted intentionally to avoid logging PII.
                Log::channel('query')->warning('Slow query detected.', [
                    'sql'          => $query->sql,
                    'time_ms'      => $query->time,
                    'connection'   => $query->connectionName,
                ]);
            }
        });
    }
}
