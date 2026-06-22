<?php

declare(strict_types=1);

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that is utilized to write
    | messages to your logs. The value provided here should match one of
    | the channels present in the list of "channels" configured below.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'deprecations'),
        'trace'   => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Laravel
    | utilizes the Monolog PHP logging library, which includes a variety
    | of powerful log handlers and formatters that you're free to use.
    |
    | Available drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog", "custom", "stack"
    |
    | SECURITY: Logs MUST NOT contain passwords, session tokens, API keys,
    | or any PII beyond what is strictly necessary for debugging.
    |
    */

    'channels' => [

        /*
         * Default production stack: daily rotating file + stderr for
         * container/cloud environments.
         */
        'stack' => [
            'driver'            => 'stack',
            'channels'          => explode(',', env('LOG_STACK', 'daily,stderr')),
            'ignore_exceptions' => false,
        ],

        /*
         * General application log – single file for local development.
         */
        'single' => [
            'driver'               => 'single',
            'path'                 => storage_path('logs/laravel.log'),
            'level'                => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        /*
         * Production-primary channel: 30-day retention, warning+ level.
         * Level is lowered to "debug" via .env during local development.
         */
        'daily' => [
            'driver'               => 'daily',
            'path'                 => storage_path('logs/laravel.log'),
            'level'                => env('LOG_LEVEL', 'warning'),
            'days'                 => (int) env('LOG_DAILY_DAYS', 30),
            'replace_placeholders' => true,
        ],

        /*
         * Dedicated security audit channel.
         * Used for authentication events, authorisation failures, suspicious
         * activity. Kept separate so it can be forwarded to a SIEM.
         */
        'security' => [
            'driver'               => 'daily',
            'path'                 => storage_path('logs/security.log'),
            'level'                => 'info',
            'days'                 => (int) env('LOG_SECURITY_DAYS', 90),
            'replace_placeholders' => true,
        ],

        /*
         * Dedicated query/performance channel – disabled in production by
         * default (null driver). Enable via LOG_QUERY_CHANNEL=query in .env.
         */
        'query' => [
            'driver'               => 'daily',
            'path'                 => storage_path('logs/query.log'),
            'level'                => 'debug',
            'days'                 => 7,
            'replace_placeholders' => true,
        ],

        /*
         * Slack alerts for critical / emergency events.
         */
        'slack' => [
            'driver'               => 'slack',
            'url'                  => env('LOG_SLACK_WEBHOOK_URL'),
            'username'             => env('LOG_SLACK_USERNAME', 'LMS Alert'),
            'emoji'                => env('LOG_SLACK_EMOJI', ':rotating_light:'),
            'level'                => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        /*
         * Stderr – ideal for Docker / Kubernetes environments where logs
         * are collected from stdout/stderr by the orchestrator.
         */
        'stderr' => [
            'driver'     => 'monolog',
            'level'      => env('LOG_LEVEL', 'warning'),
            'handler'    => StreamHandler::class,
            'formatter'  => env('LOG_STDERR_FORMATTER'),
            'with'       => [
                'stream' => 'php://stderr',
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        /*
         * Papertrail / syslog-over-UDP for hosted log aggregation services.
         */
        'papertrail' => [
            'driver'       => 'monolog',
            'level'        => env('LOG_LEVEL', 'debug'),
            'handler'      => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host'             => env('PAPERTRAIL_URL'),
                'port'             => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
            'processors'   => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver'               => 'syslog',
            'level'                => env('LOG_LEVEL', 'debug'),
            'facility'             => env('LOG_SYSLOG_FACILITY', LOG_USER),
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver'               => 'errorlog',
            'level'                => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        /*
         * Deprecation warnings channel – daily rotation, kept for 14 days.
         */
        'deprecations' => [
            'driver'               => 'daily',
            'path'                 => storage_path('logs/deprecations.log'),
            'level'                => 'warning',
            'days'                 => 14,
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver'  => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/emergency.log'),
        ],

    ],

];
