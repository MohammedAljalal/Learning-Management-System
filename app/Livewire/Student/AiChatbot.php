<?php

declare(strict_types=1);

namespace App\Livewire\Student;

use App\Models\AiChatSession;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\AI\AiServiceInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AiChatbot extends Component
{
    public ?Course $course = null;
    public ?Lesson $lesson = null;
    
    public ?AiChatSession $session = null;
    public string $newMessage = '';
    public bool $isOpen = false;

    public function mount(?Course $course = null, ?Lesson $lesson = null)
    {
        $this->course = $course;
        $this->lesson = $lesson;

        // Try to find an existing session for this course/lesson, or create a new one
        $this->session = AiChatSession::firstOrCreate([
            'user_id' => Auth::id(),
            'course_id' => $course?->id,
            'lesson_id' => $lesson?->id,
        ], [
            'title' => 'محادثة حول: ' . ($lesson ? $lesson->title : ($course ? $course->title : 'عام')),
        ]);
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function sendMessage(AiServiceInterface $aiService)
    {
        $messageText = trim($this->newMessage);
        if (empty($messageText)) {
            return;
        }

        // Save user message
        $this->session->messages()->create([
            'role' => 'user',
            'content' => $messageText,
        ]);

        $this->newMessage = '';

        // Build context
        $context = [
            'course_title' => $this->course?->title,
            'lesson_title' => $this->lesson?->title,
        ];

        // Call AI Service
        $response = $aiService->sendMessage($this->session, $messageText, $context);

        // Save assistant message
        $this->session->messages()->create([
            'role' => 'assistant',
            'content' => $response,
        ]);
        
        // Refresh session messages
        $this->session->load('messages');
    }

    public function render()
    {
        return view('livewire.student.ai-chatbot', [
            'messages' => $this->session ? $this->session->messages : []
        ]);
    }
}
