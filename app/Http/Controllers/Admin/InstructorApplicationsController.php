<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\InstructorStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class InstructorApplicationsController extends Controller
{
    /**
     * List all pending/reviewed instructor applications.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $applications = User::whereNotNull('instructor_status')
            ->when($status !== 'all', fn($q) => $q->where('instructor_status', $status))
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'name', 'email', 'bio', 'expertise', 'phone', 'id_front_path', 'id_back_path', 'selfie_path', 'instructor_status', 'rejection_reason', 'created_at', 'updated_at']);

        // Generate relative URLs for images to avoid domain/port mismatch
        $applications = $applications->map(function ($user) {
            return array_merge($user->toArray(), [
                'id_front_url' => $user->id_front_path ? '/storage/' . $user->id_front_path : null,
                'id_back_url'  => $user->id_back_path  ? '/storage/' . $user->id_back_path  : null,
                'selfie_url'   => $user->selfie_path   ? '/storage/' . $user->selfie_path   : null,
            ]);
        });

        return Inertia::render('admin/InstructorApplications', [
            'applications' => $applications,
            'currentStatus' => $status,
            'counts' => [
                'pending'  => User::where('instructor_status', 'pending')->count(),
                'approved' => User::where('instructor_status', 'approved')->count(),
                'rejected' => User::where('instructor_status', 'rejected')->count(),
            ],
        ]);
    }

    /**
     * Approve an instructor application.
     */
    public function approve(User $user)
    {
        $user->update([
            'instructor_status' => InstructorStatus::Approved,
            'rejection_reason'  => null,
        ]);

        // Assign Instructor role
        $user->syncRoles(['Instructor']);

        return back()->with('success', "تم قبول طلب {$user->name} وتعيينه مدرباً.");
    }

    /**
     * Reject an instructor application with a reason.
     */
    public function reject(Request $request, User $user)
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $user->update([
            'instructor_status' => InstructorStatus::Rejected,
            'rejection_reason'  => $request->reason,
        ]);

        return back()->with('success', "تم رفض طلب {$user->name}.");
    }
}
