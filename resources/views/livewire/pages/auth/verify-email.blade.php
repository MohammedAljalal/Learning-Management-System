<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="text-right" dir="rtl">
    <div class="mb-4 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
        {{ __('شكراً لتسجيلك معنا! قبل البدء، هل يمكنك تأكيد بريدك الإلكتروني من خلال النقر على الرابط الذي أرسلناه إليك للتو؟ إذا لم تتلقَ رسالة التأكيد، يسعدنا أن نرسل لك رسالة أخرى.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 p-3 rounded-lg border border-emerald-100 dark:border-emerald-800">
            {{ __('تم إرسال رابط تأكيد جديد إلى عنوان البريد الإلكتروني الذي أدخلته أثناء التسجيل.') }}
        </div>
    @endif

    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <x-primary-button wire:click="sendVerification" class="w-full sm:w-auto justify-center bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5">
            {{ __('إعادة إرسال رابط التأكيد') }}
        </x-primary-button>

        <button wire:click="logout" type="submit" class="underline text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
            {{ __('تسجيل الخروج') }}
        </button>
    </div>
</div>
