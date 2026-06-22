<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Enums\InstructorStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    /**
     * Show the multi-step application form.
     */
    public function show()
    {
        $user = Auth::user();

        // If already approved, send to instructor dashboard
        if ($user->instructor_status === InstructorStatus::Approved) {
            return redirect()->route('instructor.courses');
        }

        return Inertia::render('instructor/Apply', [
            'status'           => $user->instructor_status?->value,
            'rejection_reason' => $user->rejection_reason,
        ]);
    }

    /**
     * Save basic info (Step 1).
     */
    public function saveBasicInfo(Request $request)
    {
        $request->validate([
            'bio'       => ['required', 'string', 'min:50', 'max:1000'],
            'expertise' => ['required', 'string', 'max:255'],
            'phone'     => ['required', 'string', 'max:20'],
        ]);

        Auth::user()->update([
            'bio'       => $request->bio,
            'expertise' => $request->expertise,
            'phone'     => $request->phone,
        ]);

        return back()->with('success', 'تم حفظ البيانات الأساسية.');
    }

    /**
     * Upload ID documents (Steps 2, 3, 4).
     */
    public function uploadDocument(Request $request)
    {
        $type = $request->input('type'); // id_front | id_back | selfie
        $allowed = ['id_front', 'id_back', 'selfie'];

        if (!in_array($type, $allowed)) {
            return back()->with('error', 'نوع المستند غير صحيح.');
        }

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = Auth::user();
        $field = $type . '_path';

        // Delete old file if exists
        if ($user->$field) {
            Storage::disk('public')->delete($user->$field);
        }

        $path = $request->file('image')->store('instructor-verification/' . $user->id, 'public');

        $user->update([$field => $path]);

        return back()->with('success', 'تم رفع الصورة بنجاح.');
    }

    /**
     * Submit the application for admin review.
     */
    public function submit(Request $request)
    {
        $user = Auth::user();

        // Ensure all steps are complete
        if (!$user->bio || !$user->expertise || !$user->phone || 
            !$user->id_front_path || !$user->id_back_path || !$user->selfie_path) {
            return back()->with('error', 'يرجى إكمال جميع الخطوات قبل تقديم الطلب.');
        }

        $user->update([
            'instructor_status' => InstructorStatus::Pending,
            'rejection_reason'  => null,
        ]);

        // Notify admins (optional - fire event or notification)
        // event(new InstructorApplicationSubmitted($user));

        return redirect()->route('instructor.apply')->with('success', 'تم تقديم طلبك بنجاح. سيتم مراجعته في أقرب وقت.');
    }
    /**
     * Reset status to allow re-application after rejection.
     */
    public function reapply()
    {
        $user = Auth::user();

        // Only allow if currently rejected
        if ($user->instructor_status !== InstructorStatus::Rejected) {
            return back()->with('error', 'لا يمكنك إعادة التقديم الآن.');
        }

        $user->update([
            'instructor_status' => null,
            'rejection_reason'  => null,
        ]);

        return redirect()->route('instructor.apply')->with('success', 'يمكنك الآن تعديل بياناتك وإعادة تقديم الطلب.');
    }
}
