<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function storeLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->hasRole('Super Admin')) {
                return to_route('admin.categories.index');
            } elseif ($user->hasRole('Instructor')) {
                return to_route('instructor.courses');
            }

            // If user is a pending or rejected instructor, redirect to apply page
            if ($user->instructor_status !== null) {
                return redirect()->route('instructor.apply');
            }

            return redirect()->intended(route('dashboard', absolute: false));
        }

        return back()->withErrors([
            'email' => trans('auth.failed'),
        ]);
    }

    public function storeRegister(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'in:student,instructor'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role based on choice
        if ($request->role === 'instructor') {
            $user->assignRole('Student'); // Temporary until approved
            // Mark as wanting to be an instructor (status not set yet until they apply)
        } else {
            $user->assignRole('Student');
        }

        event(new Registered($user));
        Auth::login($user);

        // Redirect instructors to application flow
        if ($request->role === 'instructor') {
            return to_route('instructor.apply');
        }

        return to_route('dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
