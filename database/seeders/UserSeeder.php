<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\InstructorStatus;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@lms.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('Super Admin');

        // Instructor
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@lms.com'],
            [
                'name' => 'المدرب',
                'password' => Hash::make('password'),
                'instructor_status' => InstructorStatus::Approved,
                'bio' => 'مدرب محترف بخبرة 10 سنوات',
                'expertise' => 'البرمجة',
            ]
        );
        $instructor->assignRole('Instructor');

        // Student
        $student = User::firstOrCreate(
            ['email' => 'student@lms.com'],
            [
                'name' => 'طالب تجريبي',
                'password' => Hash::make('password'),
            ]
        );
        $student->assignRole('Student');
    }
}
