<?php

use App\Models\User;
use App\Services\Users\UserProfileService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $avatar;

    public function mount(): void
    {
        $this->name  = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(UserProfileService $profileService): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->avatar) {
            $profileService->updateAvatar($user, $this->avatar);
            $this->avatar = null;
        }

        $user->fill([
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="space-y-6" dir="rtl">
    <header>
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">
            معلومات الملف الشخصي
        </h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            قم بتحديث اسمك وبريدك الإلكتروني وصورتك الشخصية.
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">

        {{-- Avatar --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">الصورة الشخصية</label>
            <div class="flex items-center gap-4">
                @if (Auth::user()->avatar)
                    <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar"
                         class="w-16 h-16 rounded-full object-cover ring-2 ring-indigo-300">
                @else
                    <div class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                        <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                @endif
                <div class="flex-1">
                    <input wire:model="avatar" id="avatar" name="avatar" type="file" accept="image/*"
                           class="block w-full text-sm text-slate-500 dark:text-slate-400
                                  file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100 dark:file:bg-indigo-900/40 dark:file:text-indigo-300"/>
                    <p class="text-xs text-slate-400 mt-1">PNG، JPG بحد أقصى 2MB</p>
                </div>
            </div>
            @error('avatar')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
            <div wire:loading wire:target="avatar" class="text-sm text-indigo-500 mt-2 flex items-center gap-1">
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                جاري رفع الصورة...
            </div>
        </div>

        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">الاسم الكامل</label>
            <input wire:model="name" id="name" type="text" required autofocus autocomplete="name"
                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800
                          text-slate-900 dark:text-white px-4 py-2.5 text-sm shadow-sm
                          focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"/>
            @error('name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">البريد الإلكتروني</label>
            <input wire:model="email" id="email" type="email" required autocomplete="username"
                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800
                          text-slate-900 dark:text-white px-4 py-2.5 text-sm shadow-sm
                          focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"/>
            @error('email')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                <div class="mt-2 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                    <p class="text-sm text-amber-700 dark:text-amber-400">
                        بريدك الإلكتروني غير موثّق.
                        <button wire:click.prevent="sendVerification"
                                class="underline font-medium hover:text-amber-900 dark:hover:text-amber-200 ms-1">
                            انقر هنا لإعادة إرسال رابط التوثيق.
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                            تم إرسال رابط التوثيق إلى بريدك الإلكتروني.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
                           py-2.5 px-6 rounded-xl transition-colors shadow-sm shadow-indigo-600/30">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                حفظ التغييرات
            </button>

            <x-action-message class="text-sm text-emerald-600 dark:text-emerald-400 font-medium" on="profile-updated">
                ✓ تم الحفظ بنجاح
            </x-action-message>
        </div>
    </form>
</section>
