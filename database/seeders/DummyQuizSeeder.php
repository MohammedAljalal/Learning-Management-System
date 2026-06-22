<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyQuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = \App\Models\Course::with('sections')->get();

        foreach ($courses as $course) {
            // 1. Create a Final Exam for the Course
            $finalExam = \App\Models\Quiz::create([
                'course_id' => $course->id,
                'is_final_exam' => true,
                'title' => 'الاختبار النهائي للدورة: ' . $course->title,
                'description' => 'هذا هو الاختبار النهائي الشامل لتقييم استيعابك لمحتوى الدورة بالكامل.',
                'time_limit_minutes' => 60,
            ]);

            // Add 5 Questions to Final Exam
            for ($i = 1; $i <= 5; $i++) {
                $question = $finalExam->questions()->create([
                    'type' => 'single_choice',
                    'text' => "السؤال رقم {$i} في الاختبار النهائي - ما هو الخيار الصحيح؟",
                    'points' => 20,
                ]);

                // Add Options
                $correctIndex = rand(1, 4);
                for ($o = 1; $o <= 4; $o++) {
                    $question->options()->create([
                        'text' => "الخيار {$o}",
                        'is_correct' => ($o === $correctIndex),
                    ]);
                }
            }

            // 2. Create Quizzes for each Section
            foreach ($course->sections as $index => $section) {
                $sectionQuiz = \App\Models\Quiz::create([
                    'section_id' => $section->id,
                    'title' => 'اختبار الوحدة: ' . $section->title,
                    'description' => 'اختبار قصير لمراجعة ما تعلمته في هذه الوحدة.',
                    'time_limit_minutes' => 15,
                ]);

                // Add 3 Questions to Section Quiz
                for ($i = 1; $i <= 3; $i++) {
                    $question = $sectionQuiz->questions()->create([
                        'type' => 'single_choice',
                        'text' => "سؤال {$i} للوحدة {$index} - اختبر معلوماتك؟",
                        'points' => 10,
                    ]);

                    // Add Options
                    $correctIndex = rand(1, 3);
                    for ($o = 1; $o <= 3; $o++) {
                        $question->options()->create([
                            'text' => "إجابة محتملة {$o}",
                            'is_correct' => ($o === $correctIndex),
                        ]);
                    }
                }
            }
        }
    }
}
