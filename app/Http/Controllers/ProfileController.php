<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'bio'    => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user->fill([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'bio'   => $validated['bio'] ?? $user->bio,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'تم تحديث كلمة المرور بنجاح');
    }
}
