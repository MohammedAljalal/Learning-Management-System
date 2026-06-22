<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" dir="rtl"
     class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Logo + Primary Nav --}}
            <div class="flex items-center gap-8">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                        </svg>
                    </div>
                    <span class="font-black text-slate-900 dark:text-white text-lg">{{ config('app.name') }}</span>
                </a>

                {{-- Desktop Nav Links --}}
                <div class="hidden sm:flex items-center gap-1">
                    <a href="{{ route('courses.catalog') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('courses.*') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        الدورات
                    </a>
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                        لوحتي
                    </a>
                    @role('Instructor')
                        <a href="{{ route('instructor.courses') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('instructor.courses*') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            دوراتي
                        </a>
                        <a href="{{ route('instructor.financials') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('instructor.financials') ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            المالية
                        </a>
                    @endrole
                    @role('Super Admin')
                        <a href="{{ route('admin.categories') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium transition-colors text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                            الإدارة
                        </a>
                    @endrole
                </div>
            </div>

            {{-- Right side: Notifications + Profile --}}
            <div class="hidden sm:flex items-center gap-3">

                {{-- Notification Bell (auth users only) --}}
                @auth
                    <livewire:student.notification-dropdown />
                @endauth

                {{-- Profile Dropdown --}}
                @auth
                    <div x-data="{ profileOpen: false }" class="relative" @click.outside="profileOpen = false">
                        <button @click="profileOpen = !profileOpen"
                                class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 text-xs font-bold">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="hidden md:block" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="profileOpen" style="display:none"
                             x-transition
                             class="absolute left-0 top-full mt-1 w-52 bg-white dark:bg-slate-800 rounded-2xl shadow-lg ring-1 ring-black/5 dark:ring-white/10 py-1 z-50">
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('profile') }}"
                               class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                الملف الشخصي
                            </a>
                            <div class="border-t border-slate-100 dark:border-slate-700 mt-1">
                                <button wire:click="logout"
                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    تسجيل الخروج
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors px-3 py-2">
                        تسجيل الدخول
                    </a>
                    <a href="{{ route('register') }}" class="text-sm font-bold bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl transition-colors shadow-md shadow-indigo-600/20">
                        ابدأ مجاناً
                    </a>
                @endauth
            </div>

            {{-- Hamburger --}}
            <div class="flex items-center sm:hidden">
                <button @click="open = !open"
                        class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-slate-200 dark:border-slate-800">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('courses.catalog') }}" class="block px-3 py-2.5 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                الدورات
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="block px-3 py-2.5 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    لوحتي
                </a>
                @role('Instructor')
                    <a href="{{ route('instructor.courses') }}" class="block px-3 py-2.5 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        دوراتي (مدرب)
                    </a>
                    <a href="{{ route('instructor.financials') }}" class="block px-3 py-2.5 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        المالية (مدرب)
                    </a>
                @endrole
                @role('Super Admin')
                    <a href="{{ route('admin.categories') }}" class="block px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        الإدارة
                    </a>
                @endrole
                <div class="pt-3 mt-3 border-t border-slate-200 dark:border-slate-700 space-y-1">
                    <div class="px-3 pb-2">
                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('profile') }}" class="block px-3 py-2.5 rounded-xl text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        الملف الشخصي
                    </a>
                    <button wire:click="logout" class="w-full text-right px-3 py-2.5 rounded-xl text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        تسجيل الخروج
                    </button>
                </div>
            @else
                <div class="flex gap-2 pt-2">
                    <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2 rounded-xl text-sm font-medium border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2 rounded-xl text-sm font-bold bg-indigo-600 text-white">ابدأ مجاناً</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
