<?php

namespace App\Models;

use App\Enums\InstructorStatus;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'balance',
        'avatar',
        'instructor_status',
        'bio',
        'expertise',
        'phone',
        'id_front_path',
        'id_back_path',
        'selfie_path',
        'rejection_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
            'instructor_status' => InstructorStatus::class,
        ];
    }

    /**
     * Get the courses that the user is instructing.
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get the courses this user is enrolled in (as Student).
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the purchases (transactions) made by this student.
     */
    public function purchases()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Get all lesson progress records for this user.
     */
    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Get the quiz attempts for this user.
     */
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get the certificates for this user.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the XP transactions for this user.
     */
    public function xpTransactions()
    {
        return $this->hasMany(XpTransaction::class);
    }

    /**
     * Get the AI chat sessions for this user.
     */
    public function aiChatSessions()
    {
        return $this->hasMany(AiChatSession::class);
    }

    /**
     * Compute total XP based on transactions.
     */
    public function getTotalXpAttribute(): int
    {
        return (int) $this->xpTransactions()->sum('amount');
    }

    /**
     * Compute user's level based on total XP.
     * Example logic: 500 XP per level.
     */
    public function getLevelAttribute(): int
    {
        return (int) floor($this->total_xp / 500) + 1;
    }
}
