<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Models\AiChatSession;

interface AiServiceInterface
{
    /**
     * Send a message to the AI provider, along with the chat session context.
     * 
     * @param AiChatSession $session The chat session (containing message history)
     * @param string $message The new message from the user
     * @param array $context Optional contextual data (e.g., current course/lesson text)
     * @return string The AI's response text
     */
    public function sendMessage(AiChatSession $session, string $message, array $context = []): string;
}
