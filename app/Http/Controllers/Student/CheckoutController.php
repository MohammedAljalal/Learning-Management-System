<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function show(Course $course)
    {
        // If already enrolled, redirect to course
        if (auth()->user()->enrollments()->where('course_id', $course->id)->exists()) {
            return redirect()->route('courses.show', $course->slug);
        }

        // If course is free, redirect to show page to enroll directly
        if ($course->price <= 0) {
            return redirect()->route('courses.show', $course->slug);
        }

        $course->load(['instructor:id,name']);
        $course->thumbnail_url = $course->getFirstMediaUrl('thumbnail');

        return Inertia::render('student/Checkout', [
            'course' => $course
        ]);
    }

    public function process(Request $request, Course $course)
    {
        $request->validate([
            'nameOnCard' => 'required|string|max:255',
            'cardNumber' => 'required|string|min:16|max:19',
            'expiryDate' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/?([0-9]{2})$/'],
            'cvc' => 'required|string|min:3|max:4',
        ]);

        // Clean card number
        $cleanCard = preg_replace('/\D/', '', $request->cardNumber);

        // Simple mock logic: only accept test card starting with 4242
        if (!str_starts_with($cleanCard, '4242')) {
            throw ValidationException::withMessages([
                'cardNumber' => 'تم رفض البطاقة. يرجى استخدام بطاقة اختبار صحيحة (مثل 4242).',
            ]);
        }

        DB::transaction(function () use ($course) {
            $user = auth()->user();
            $amount = $course->price;
            
            // Calculate revenue split (Platform 20%, Instructor 80%)
            $platformFee = $amount * 0.20;
            $instructorRevenue = $amount - $platformFee;

            // 1. Record Transaction
            Transaction::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount' => $amount,
                'platform_fee' => $platformFee,
                'instructor_revenue' => $instructorRevenue,
                'payment_method' => 'mock_card',
                'status' => 'completed',
            ]);

            // 2. Add revenue to instructor's balance
            $instructor = $course->instructor;
            $instructor->balance += $instructorRevenue;
            $instructor->save();

            // 3. Enroll Student
            $user->enrollments()->create([
                'course_id' => $course->id,
                'enrolled_at' => now(),
            ]);
        });

        // Try to get first lesson to redirect to
        $firstSection = $course->sections()->orderBy('order')->first();
        $firstLesson = $firstSection ? $firstSection->lessons()->orderBy('order')->first() : null;
        
        if ($firstLesson) {
            return redirect()->route('courses.learn', [$course->slug, $firstLesson->id])
                ->with('success', 'تمت عملية الدفع بنجاح! شكراً لشرائك الدورة.');
        }

        return redirect()->route('courses.show', $course->slug)
            ->with('success', 'تمت عملية الدفع بنجاح! شكراً لشرائك الدورة.');
    }
}
