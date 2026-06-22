<?php

declare(strict_types=1);

namespace App\Services;

use Psr\Log\LoggerInterface;

/**
 * Abstract Base Service
 *
 * Services coordinate domain logic across repositories, value objects,
 * and events. They sit between the Action (use-case) layer and the
 * repository/infrastructure layer.
 *
 * SOLID:
 *  - Each concrete service handles ONE domain area (SRP).
 *  - Depend on interfaces, not concretions (DIP).
 *
 * Domain services extend this and inject their required dependencies
 * via constructor promotion.
 */
abstract class Service
{
    public function __construct(
        protected readonly LoggerInterface $logger,
    ) {}
}
