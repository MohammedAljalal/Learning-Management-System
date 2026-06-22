<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::with('roles')->get();

if ($users->isEmpty()) {
    echo "No users found. Creating test accounts...\n";
    
    // Super Admin
    $superAdmin = new \App\Models\User();
    $superAdmin->name = 'Super Admin User';
    $superAdmin->email = 'superadmin@example.com';
    $superAdmin->password = \Illuminate\Support\Facades\Hash::make('password123');
    $superAdmin->email_verified_at = now();
    $superAdmin->save();
    $superAdmin->assignRole('Super Admin');


    // Instructor
    $instructor = new \App\Models\User();
    $instructor->name = 'Instructor User';
    $instructor->email = 'instructor@example.com';
    $instructor->password = \Illuminate\Support\Facades\Hash::make('password123');
    $instructor->email_verified_at = now();
    $instructor->save();
    $instructor->assignRole('Instructor');

    // Student
    $student = new \App\Models\User();
    $student->name = 'Student User';
    $student->email = 'student@example.com';
    $student->password = \Illuminate\Support\Facades\Hash::make('password123');
    $student->email_verified_at = now();
    $student->save();
    $student->assignRole('Student');

    $users = \App\Models\User::with('roles')->get();
}

$output = $users->map(function($u) {
    return [
        'name' => $u->name,
        'email' => $u->email,
        'roles' => $u->roles->pluck('name')->toArray()
    ];
})->toArray();

echo json_encode($output, JSON_PRETTY_PRINT);

