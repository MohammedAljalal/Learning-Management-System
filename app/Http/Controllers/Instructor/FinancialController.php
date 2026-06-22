<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FinancialController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        // Get all course IDs for this instructor
        $courseIds = Course::where('instructor_id', $user->id)->pluck('id');

        // Total earnings = sum of instructor_revenue from completed transactions
        $totalEarnings = DB::table('transactions')
            ->whereIn('course_id', $courseIds)
            ->where('status', 'completed')
            ->sum('instructor_revenue');

        // For balance: same as totalEarnings (no withdrawal system yet)
        $balance = $totalEarnings;

        // Get recent transactions with course info
        $transactions = DB::table('transactions')
            ->join('courses', 'transactions.course_id', '=', 'courses.id')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->whereIn('transactions.course_id', $courseIds)
            ->where('transactions.status', 'completed')
            ->orderByDesc('transactions.created_at')
            ->select([
                'transactions.id',
                'transactions.amount',
                'transactions.instructor_revenue',
                'transactions.platform_fee',
                'transactions.payment_method',
                'transactions.status',
                'transactions.created_at',
                'courses.title as course_title',
                'users.name as student_name',
            ])
            ->paginate(15);

        // Monthly earnings for chart (last 6 months)
        $monthlyEarnings = DB::table('transactions')
            ->whereIn('course_id', $courseIds)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(instructor_revenue) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($row) => [
                'month' => $row->month,
                'total' => (float) $row->total,
            ]);

        // Total students (unique) across all instructor courses
        $totalStudents = DB::table('enrollments')
            ->whereIn('course_id', $courseIds)
            ->count();

        return Inertia::render('instructor/Financials', [
            'balance'        => (float) $balance,
            'totalEarnings'  => (float) $totalEarnings,
            'totalStudents'  => $totalStudents,
            'monthlyEarnings' => $monthlyEarnings,
            'transactions'   => $transactions,
        ]);
    }
}
