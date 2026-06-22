<?php

declare(strict_types=1);

namespace App\Actions;

/**
 * Abstract Action Base
 *
 * Actions encapsulate a single unit of application-layer business logic.
 * They are the "Use Case" layer following the Single Responsibility Principle.
 *
 * Rules:
 *  - One action = one use case (e.g., EnrollStudentAction, PublishCourseAction).
 *  - Actions MUST NOT directly depend on HTTP Request objects.
 *  - Actions accept DTOs or scalar types, return domain models or DTOs.
 *  - Actions are invokable via __invoke() for clean DI container resolution.
 *
 * Usage:
 *   class EnrollStudentAction extends Action
 *   {
 *       public function execute(EnrollStudentDto $dto): Enrollment { ... }
 *   }
 */
abstract class Action
{
    // Concrete actions MUST implement an execute() method with typed
    // parameters appropriate to their use case. No shared interface is
    // enforced here intentionally to allow each action to define its own
    // signature — PHP does not support typed generics for return types.
}
