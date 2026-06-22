<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AiChatSession;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\AI\AiServiceInterface;
use Illuminate\Support\Facades\Auth;

class AiChatController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lesson_id' => 'nullable|exists:lessons,id',
        ]);

        $courseId = $request->input('course_id');
        $lessonId = $request->input('lesson_id');

        $course = Course::find($courseId);
        $lesson = $lessonId ? Lesson::find($lessonId) : null;

        $session = AiChatSession::firstOrCreate([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'lesson_id' => $lesson?->id,
        ], [
            'title' => 'محادثة حول: ' . ($lesson ? $lesson->title : $course->title),
        ]);

        return response()->json([
            'session' => $session,
            'messages' => $session->messages()->orderBy('created_at', 'asc')->get(),
        ]);
    }

    public function store(Request $request, AiServiceInterface $aiService)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'message' => 'required|string|max:1000',
        ]);

        $course = Course::find($request->course_id);
        $lesson = $request->lesson_id ? Lesson::find($request->lesson_id) : null;

        $session = AiChatSession::firstOrCreate([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'lesson_id' => $lesson?->id,
        ], [
            'title' => 'محادثة حول: ' . ($lesson ? $lesson->title : $course->title),
        ]);

        // Save user message
        $userMessage = $session->messages()->create([
            'role' => 'user',
            'content' => $request->message,
        ]);

        // Build context
        $context = [
            'course_title' => $course->title,
            'lesson_title' => $lesson?->title,
        ];

        // Call AI Service
        try {
            $response = $aiService->sendMessage($session, $request->message, $context);

            // Save assistant message
            $assistantMessage = $session->messages()->create([
                'role' => 'assistant',
                'content' => $response,
            ]);

            return response()->json([
                'userMessage' => $userMessage,
                'assistantMessage' => $assistantMessage,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء الاتصال بالذكاء الاصطناعي'], 500);
        }
    }
}
