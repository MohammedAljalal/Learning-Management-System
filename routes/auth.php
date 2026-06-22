<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('register', fn() => Inertia::render('auth/Register'))
        ->name('register');

    Route::post('register', [AuthController::class, 'storeRegister']);

    Route::get('login', fn() => Inertia::render('auth/Login'))
        ->name('login');
        
    Route::post('login', [AuthController::class, 'storeLogin']);

    Route::get('forgot-password', fn() => Inertia::render('auth/ForgotPassword', ['status' => session('status')]))
        ->name('password.request');

    Route::post('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', fn($token) => Inertia::render('auth/ResetPassword', [
            'token' => $token,
            'email' => request()->query('email')
        ]))
        ->name('password.reset');

    Route::post('reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', fn() => Inertia::render('auth/VerifyEmail'))
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('confirm-password', fn() => Inertia::render('auth/ConfirmPassword'))
        ->name('password.confirm');

    Route::post('logout', [AuthController::class, 'destroy'])
        ->name('logout');
});
