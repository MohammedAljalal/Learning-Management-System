<?php

namespace App\Livewire\Student;

use App\Models\Course;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

class Checkout extends Component
{
    public Course $course;

    public $nameOnCard = '';
    public $cardNumber = '';
    public $expiryDate = '';
    public $cvc = '';

    public function mount(Course $course)
    {
        // If already enrolled, redirect to course
        if (auth()->user()->enrollments()->where('course_id', $course->id)->exists()) {
            return redirect()->route('courses.show', $course->slug);
        }

        // If course is free, redirect to show page to enroll directly
        if ($course->price <= 0) {
            return redirect()->route('courses.show', $course->slug);
        }

        $this->course = $course;
        $this->nameOnCard = auth()->user()->name;
    }

    protected $rules = [
        'nameOnCard' => 'required|string|max:255',
        'cardNumber' => 'required|string|min:16|max:19',
        'expiryDate' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/?([0-9]{2})$/'],
        'cvc' => 'required|string|min:3|max:4',
    ];

    public function processPayment()
    {
        $this->validate();

        // Simulate network delay
        sleep(1);

        // Clean card number
        $cleanCard = preg_replace('/\D/', '', $this->cardNumber);

        // Simple mock logic: only accept test card starting with 4242
        if (!str_starts_with($cleanCard, '4242')) {
            throw ValidationException::withMessages([
                'cardNumber' => 'تم رفض البطاقة. يرجى استخدام بطاقة اختبار صحيحة (مثل 4242).',
            ]);
        }

        DB::transaction(function () {
            $user = auth()->user();
            $amount = $this->course->price;
            
            // Calculate revenue split (Platform 20%, Instructor 80%)
            $platformFee = $amount * 0.20;
            $instructorRevenue = $amount - $platformFee;

            // 1. Record Transaction
            Transaction::create([
                'user_id' => $user->id,
                'course_id' => $this->course->id,
                'amount' => $amount,
                'platform_fee' => $platformFee,
                'instructor_revenue' => $instructorRevenue,
                'payment_method' => 'mock_card',
                'status' => 'completed',
            ]);

            // 2. Add revenue to instructor's balance
            $instructor = $this->course->instructor;
            $instructor->balance += $instructorRevenue;
            $instructor->save();

            // 3. Enroll Student
            $user->enrollments()->create([
                'course_id' => $this->course->id,
                'enrolled_at' => now(),
            ]);
        });

        session()->flash('status', 'تمت عملية الدفع بنجاح! شكراً لشرائك الدورة.');

        $firstSection = $this->course->sections()->orderBy('order')->first();
        $firstLesson = $firstSection ? $firstSection->lessons()->orderBy('order')->first() : null;
        
        if ($firstLesson) {
            return redirect()->route('courses.learn', [$this->course->slug, $firstLesson->id]);
        }

        return redirect()->route('courses.show', $this->course->slug);
    }

    public function render()
    {
        return view('livewire.student.checkout')->layout('layouts.app');
    }
}
