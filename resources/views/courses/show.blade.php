<x-app-layout>

@push('meta')
    <meta name="description" content="{{ Str::limit($course->description, 160) }}">
@endpush

@php
    $difficultyLabel = match($course->difficulty->value) {
        'beginner' => 'مبتدئ',
        'intermediate' => 'متوسط',
        'expert' => 'خبير',
        default => $course->difficulty->value,
    };
    $difficultyColor = match($course->difficulty->value) {
        'beginner' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400',
        'intermediate' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
        'expert' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
        default => 'bg-slate-100 text-slate-700',
    };
@endphp

<div class="py-12" dir="rtl">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Course Header --}}
                <div>
                    {{-- Breadcrumb --}}
                    <nav class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 mb-4">
                        <a href="{{ route('courses.catalog') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">الدورات</a>
                        <svg class="w-4 h-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        @if($course->category)
                            <a href="{{ route('courses.catalog') }}?categoryFilter={{ $course->category->id }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ $course->category->name }}</a>
                            <svg class="w-4 h-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        @endif
                        <span class="text-slate-800 dark:text-slate-200 font-medium truncate">{{ $course->title }}</span>
                    </nav>

                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white leading-tight mb-3">{{ $course->title }}</h1>
                    <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed">{{ $course->description }}</p>

                    {{-- Tags Row --}}
                    <div class="flex flex-wrap gap-2 mt-4">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $difficultyColor }}">{{ $difficultyLabel }}</span>
                        @if($course->category)
                            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ $course->category->name }}</span>
                        @endif
                    </div>
                </div>

                {{-- Thumbnail --}}
                <div class="aspect-video rounded-2xl overflow-hidden bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg">
                    @if($course->getFirstMediaUrl('thumbnail'))
                        <img src="{{ $course->getFirstMediaUrl('thumbnail') }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Course Curriculum --}}
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">محتوى الدورة</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            {{ $course->sections->count() }} وحدات ·
                            {{ $course->sections->sum(fn($s) => $s->lessons->count()) }} درس
                        </p>
                    </div>

                    @if($course->sections->isEmpty())
                        <div class="p-8 text-center text-slate-500 dark:text-slate-400 text-sm">
                            لم يتم إضافة محتوى بعد.
                        </div>
                    @else
                        <div x-data="{ openSection: 0 }" class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($course->sections as $index => $section)
                                <div>
                                    <button
                                        @click="openSection = openSection === {{ $index }} ? null : {{ $index }}"
                                        class="w-full flex items-center justify-between px-6 py-4 text-right hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center shrink-0">
                                                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ $index + 1 }}</span>
                                            </div>
                                            <span class="font-semibold text-slate-800 dark:text-slate-200 text-sm">{{ $section->title }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs text-slate-400">{{ $section->lessons->count() }} درس</span>
                                            <svg class="w-4 h-4 text-slate-400 transition-transform" :class="openSection === {{ $index }} ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </button>

                                    <div x-show="openSection === {{ $index }}" x-collapse class="border-t border-slate-100 dark:border-slate-700">
                                        @foreach($section->lessons as $lesson)
                                            <div class="flex items-center gap-3 px-6 py-3 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-700/40 transition-colors">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="text-sm text-slate-700 dark:text-slate-300">{{ $lesson->title }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sticky Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg sticky top-6 overflow-hidden">
                    {{-- Enrollment Count --}}
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ number_format($course->enrollments->count()) }} طالب مسجّل</span>
                        </div>

                        {{-- Price Display --}}
                        <div class="flex items-center justify-between py-2 border-t border-slate-100 dark:border-slate-700">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">سعر الدورة:</span>
                            @if($course->price > 0)
                                <span class="text-2xl font-bold text-slate-900 dark:text-white">${{ number_format($course->price, 2) }}</span>
                            @else
                                <span class="text-xl font-bold text-emerald-500">مجانًا</span>
                            @endif
                        </div>

                        {{-- Instructor Info --}}
                        @if($course->instructor)
                            <div class="flex items-center gap-3 py-4 border-t border-slate-100 dark:border-slate-700">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 overflow-hidden shrink-0">
                                    @if($course->instructor->avatar)
                                        <img src="{{ asset('storage/' . $course->instructor->avatar) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold">
                                            {{ mb_substr($course->instructor->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">المدرب</p>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $course->instructor->name }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Start Learning CTA --}}
                        @auth
                            @if(auth()->user()->hasRole('Student'))
                                @if(auth()->user()->enrollments()->where('course_id', $course->id)->exists())
                                    @if($course->sections->isNotEmpty() && $course->sections->first()->lessons->isNotEmpty())
                                        <a href="{{ route('courses.learn', [$course->slug, $course->sections->first()->lessons->first()->id]) }}"
                                           class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition-colors shadow-lg shadow-indigo-600/30">
                                            متابعة التعلم
                                        </a>
                                    @else
                                        <button disabled class="block w-full text-center bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400 font-bold py-3 px-6 rounded-xl cursor-not-allowed">
                                            لا يوجد محتوى بعد
                                        </button>
                                    @endif
                                @else
                                    @if($course->price > 0)
                                        <a href="{{ route('checkout', $course->slug) }}" class="block w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl transition-colors shadow-lg shadow-emerald-600/30">
                                            اشترِ الدورة الآن (${{ number_format($course->price, 2) }})
                                        </a>
                                    @else
                                        <form action="{{ route('courses.enroll', $course->slug) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition-colors shadow-lg shadow-indigo-600/30">
                                                سجّل في الدورة مجاناً
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            @else
                                @if($course->sections->isNotEmpty() && $course->sections->first()->lessons->isNotEmpty())
                                    <a href="{{ route('courses.learn', [$course->slug, $course->sections->first()->lessons->first()->id]) }}"
                                       class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition-colors shadow-lg shadow-indigo-600/30">
                                        استعراض محتوى الدورة
                                    </a>
                                @endif
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                               class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition-colors shadow-lg shadow-indigo-600/30">
                                سجّل دخولك للبدء
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</x-app-layout>
