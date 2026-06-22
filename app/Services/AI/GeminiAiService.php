<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Models\AiChatSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAiService implements AiServiceInterface
{
    private string $apiKey;
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY', ''));
    }

    public function sendMessage(AiChatSession $session, string $message, array $context = []): string
    {
        if (empty($this->apiKey)) {
            Log::warning('Gemini API key is not set. Falling back to mock response.');
            return "عذراً، لم يتم إعداد مفتاح API الخاص بالذكاء الاصطناعي.";
        }

        $systemPrompt = "أنت المساعد الذكي الخاص لمنصة LMS التعليمية (LMS Smart Assistant). دورك هو مساعدة الطلاب في الإجابة على استفساراتهم المتعلقة بالدورات والدروس. 
يجب أن تكون إجاباتك ودية، واضحة، باللغة العربية، ومركزة على مساعدة الطالب في فهم المحتوى التعليمي. 
في حال سألك الطالب 'من أنت؟' أو أي سؤال مشابه، أجب بشكل حصري بأنك 'المساعد الذكي لمنصة LMS، ومهمتي هي مرافقتك في رحلتك التعليمية ومساعدتك على فهم الدروس بأفضل طريقة ممكنة'.
";
        if (isset($context['course_title'])) {
            $systemPrompt .= "\nالدورة الحالية للطالب هي: " . $context['course_title'];
        }
        if (isset($context['lesson_title'])) {
            $systemPrompt .= "\nالدرس الحالي للطالب هو: " . $context['lesson_title'];
        }

        // Format history for Gemini API
        $contents = [];
        foreach ($session->messages as $msg) {
            $contents[] = [
                'role' => $msg->role === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $msg->content]]
            ];
        }

        // Add the current message (we don't need to add it again if the controller already saved it to DB before calling, 
        // wait, let's check controller. The controller saves the user message to DB before calling this method, so it's already in $session->messages).
        
        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $systemPrompt]]
            ],
            'contents' => $contents,
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '?key=' . $this->apiKey, $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? "لم أتمكن من فهم الرد.";
            }

            Log::error('Gemini API Error: ' . $response->body());
            return "عذراً، واجهت مشكلة في الاتصال بالذكاء الاصطناعي. الرجاء المحاولة لاحقاً.";

        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
            return "عذراً، حدث خطأ غير متوقع. الرجاء المحاولة لاحقاً.";
        }
    }
}
