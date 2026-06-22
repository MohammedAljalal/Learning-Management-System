<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password'         => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->dispatch('password-updated');
    }
}; ?>

<section class="space-y-6" dir="rtl">
    <header>
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">
            تغيير كلمة المرور
        </h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            يُنصح باستخدام كلمة مرور طويلة وعشوائية للحفاظ على أمان حسابك.
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">

        {{-- Current Password --}}
        <div>
            <label for="update_password_current_password"
                   class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                كلمة المرور الحالية
            </label>
            <input wire:model="current_password"
                   id="update_password_current_password"
                   name="current_password"
                   type="password"
                   autocomplete="current-password"
                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800
                          text-slate-900 dark:text-white px-4 py-2.5 text-sm shadow-sm
                          focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"/>
            @error('current_password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- New Password --}}
        <div>
            <label for="update_password_password"
                   class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                كلمة المرور الجديدة
            </label>
            <input wire:model="password"
                   id="update_password_password"
                   name="password"
                   type="password"
                   autocomplete="new-password"
                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800
                          text-slate-900 dark:text-white px-4 py-2.5 text-sm shadow-sm
                          focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"/>
            @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="update_password_password_confirmation"
                   class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                تأكيد كلمة المرور
            </label>
            <input wire:model="password_confirmation"
                   id="update_password_password_confirmation"
                   name="password_confirmation"
                   type="password"
                   autocomplete="new-password"
                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800
                          text-slate-900 dark:text-white px-4 py-2.5 text-sm shadow-sm
                          focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"/>
            @error('password_confirmation')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
                           py-2.5 px-6 rounded-xl transition-colors shadow-sm shadow-indigo-600/30">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                تحديث كلمة المرور
            </button>

            <x-action-message class="text-sm text-emerald-600 dark:text-emerald-400 font-medium" on="password-updated">
                ✓ تم تحديث كلمة المرور
            </x-action-message>
        </div>
    </form>
</section>
