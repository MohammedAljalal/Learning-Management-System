<?php

namespace App\Livewire\Instructor;

use Livewire\Component;
use App\Models\Transaction;

class Financials extends Component
{
    public function render()
    {
        $user = auth()->user();
        
        // Sum total earnings
        $totalEarnings = Transaction::whereHas('course', function ($query) use ($user) {
            $query->where('instructor_id', $user->id);
        })->sum('instructor_revenue');

        // Recent sales
        $recentTransactions = Transaction::with(['course', 'user'])
            ->whereHas('course', function ($query) use ($user) {
                $query->where('instructor_id', $user->id);
            })
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.instructor.financials', [
            'totalEarnings' => $totalEarnings,
            'balance' => $user->balance,
            'recentTransactions' => $recentTransactions,
        ])->layout('layouts.app');
    }
}
