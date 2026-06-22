<?php

declare(strict_types=1);

namespace App\Services\Assessment;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuestionOption;
use App\Services\Service;
use App\Services\Gamification\GamificationService;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

class QuizService extends Service
{
    protected GamificationService $gamificationService;

    public function __construct(LoggerInterface $logger, GamificationService $gamificationService)
    {
        parent::__construct($logger);
        $this->gamificationService = $gamificationService;
    }

    /**
     * Start a new attempt for a student.
     */
    public function startAttempt(int $userId, Quiz $quiz): QuizAttempt
    {
        return DB::transaction(function () use ($userId, $quiz) {
            $attempt = QuizAttempt::create([
                'user_id' => $userId,
                'quiz_id' => $quiz->id,
                'started_at' => now(),
            ]);

            $this->logger->info("Quiz attempt started", ['attempt_id' => $attempt->id]);
            return $attempt;
        });
    }

    /**
     * Submit and grade the quiz attempt.
     */
    public function submitAttempt(QuizAttempt $attempt, array $answers): QuizAttempt
    {
        return DB::transaction(function () use ($attempt, $answers) {
            $quiz = $attempt->quiz;
            $quiz->load('questions.options');
            
            $totalScore = 0;
            $totalPossiblePoints = 0;

            foreach ($quiz->questions as $question) {
                $totalPossiblePoints += $question->points;
                $submittedAnswer = $answers[$question->id] ?? null;
                $pointsAwarded = 0;
                $isCorrect = false;

                if ($question->type->value === 'short_text') {
                    // Short text needs manual grading, so score is null initially
                    $attempt->answers()->create([
                        'question_id' => $question->id,
                        'answer_text' => $submittedAnswer,
                        'is_correct' => null,
                        'points_awarded' => 0,
                    ]);
                    continue;
                }

                // Auto-grade other types
                if (is_array($submittedAnswer)) {
                    // Multiple Options
                    $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->toArray();
                    sort($correctOptionIds);
                    
                    $submittedOptionIds = array_map('intval', $submittedAnswer);
                    sort($submittedOptionIds);

                    if ($correctOptionIds === $submittedOptionIds) {
                        $pointsAwarded = $question->points;
                        $isCorrect = true;
                    }

                    $attempt->answers()->create([
                        'question_id' => $question->id,
                        'is_correct' => $isCorrect,
                        'points_awarded' => $pointsAwarded,
                    ]);
                    
                    // Note: In a full system, you might save each selected option in AttemptAnswerOption pivot
                } else {
                    // Single choice or True/False
                    $selectedOptionId = (int) $submittedAnswer;
                    $option = $question->options->firstWhere('id', $selectedOptionId);
                    
                    if ($option && $option->is_correct) {
                        $pointsAwarded = $question->points;
                        $isCorrect = true;
                    }

                    $attempt->answers()->create([
                        'question_id' => $question->id,
                        'question_option_id' => $selectedOptionId ?: null,
                        'is_correct' => $isCorrect,
                        'points_awarded' => $pointsAwarded,
                    ]);
                }

                $totalScore += $pointsAwarded;
            }

            $passingScore = $totalPossiblePoints / 2;
            $isPassed = $quiz->is_practice ? true : ($totalScore >= $passingScore);

            $attempt->update([
                'completed_at' => now(),
                'score' => $quiz->is_practice ? 0 : $totalScore,
                'is_passed' => $isPassed,
            ]);

            // Gamification: Award XP if passed and not practice
            $attempt->loadMissing('user');
            if ($isPassed && !$quiz->is_practice) {
                // Example logic: award XP equal to score
                $this->gamificationService->awardXp($attempt->user, $totalScore, "اجتياز اختبار: {$quiz->title}");
                
                // Notify the user
                $attempt->user->notify(new \App\Notifications\XpAwardedNotification($totalScore, "اجتياز اختبار: {$quiz->title}"));
            }

            // Issue certificate if it's a final exam and passed
            if ($isPassed && $quiz->is_final_exam && $quiz->course_id) {
                \App\Models\Certificate::firstOrCreate(
                    [
                        'user_id' => $attempt->user_id,
                        'course_id' => $quiz->course_id,
                    ],
                    [
                        'issued_at' => now(),
                    ]
                );
            }

            $this->logger->info("Quiz attempt submitted", ['attempt_id' => $attempt->id, 'score' => $totalScore]);

            return $attempt;
        });
    }
}
