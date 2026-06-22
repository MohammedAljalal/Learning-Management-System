<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6" dir="rtl">
    <header>
        <h2 class="text-xl font-bold text-red-600 dark:text-red-400">
            حذف الحساب
        </h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            بمجرد حذف حسابك، سيتم حذف جميع بياناتك ومعلوماتك بشكل نهائي ولا يمكن التراجع عن ذلك.
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white
               font-semibold py-2.5 px-6 rounded-xl transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        حذف الحساب
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6" dir="rtl">

            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                هل أنت متأكد من حذف حسابك؟
            </h2>

            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                لا يمكن التراجع عن هذا الإجراء. سيتم حذف جميع بياناتك نهائياً.
                أدخل كلمة المرور للتأكيد.
            </p>

            <div class="mt-6">
                <label for="delete_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    كلمة المرور
                </label>
                <input wire:model="password"
                       id="delete_password"
                       name="password"
                       type="password"
                       placeholder="أدخل كلمة المرور للتأكيد"
                       class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800
                              text-slate-900 dark:text-white px-4 py-2.5 text-sm shadow-sm
                              focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none transition"/>
                @error('password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-start gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white
                               font-semibold py-2.5 px-6 rounded-xl transition-colors">
                    نعم، احذف حسابي
                </button>
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600
                               text-slate-700 dark:text-slate-300 font-semibold py-2.5 px-6 rounded-xl transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </x-modal>
</section>
