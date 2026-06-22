<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Models\AiChatSession;
use Psr\Log\LoggerInterface;

class MockAiService implements AiServiceInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function sendMessage(AiChatSession $session, string $message, array $context = []): string
    {
        $this->logger->info("Mock AI Service called", [
            'session_id' => $session->id,
            'message' => $message,
            'context' => $context,
        ]);

        // Simulate network delay logic removed to prevent blocking PHP workers

        // Generate a contextual mock response
        $courseTitle = $context['course_title'] ?? 'this course';
        $lessonTitle = $context['lesson_title'] ?? 'this lesson';

        return "This is a mock AI response. I see you are asking about: \"{$message}\" while viewing {$lessonTitle} in {$courseTitle}. How else can I assist you?";
    }
}
