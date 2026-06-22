<?php

declare(strict_types=1);

namespace App\Livewire\Student;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public bool $isOpen = false;

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function loadNotifications(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->notifications = $user->notifications()->take(10)->get();
            $this->unreadCount   = $user->unreadNotifications()->count();
        }
    }

    /**
     * Called by Livewire polling (wire:poll) every 10 seconds.
     */
    public function poll(): void
    {
        $this->loadNotifications();
    }

    public function toggle(): void
    {
        $this->isOpen = !$this->isOpen;

        if ($this->isOpen && Auth::check()) {
            Auth::user()->unreadNotifications->markAsRead();
            $this->unreadCount = 0;
        }
    }

    public function markAllRead(): void
    {
        if (Auth::check()) {
            Auth::user()->unreadNotifications->markAsRead();
            $this->unreadCount = 0;
            $this->loadNotifications();
        }
    }

    public function render()
    {
        return view('livewire.student.notification-dropdown');
    }
}
