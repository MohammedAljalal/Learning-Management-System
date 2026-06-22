<x-slot name="header">
    <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
        {{ __('الدورات التدريبية') }}
    </h2>
</x-slot>

<div class="py-12" dir="rtl">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Search & Filters Bar --}}
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm p-4 mb-8 flex flex-col md:flex-row gap-4 items-center">
            {{-- Live Search --}}
            <div class="relative flex-1 w-full">
                <svg class="absolute end-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="search"
                    placeholder="ابحث عن دورة أو مدرب..."
                    class="w-full ps-4 pe-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm"
                >
            </div>

            {{-- Category Filter --}}
            <select wire:model.live="categoryFilter" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2.5 px-4 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                <option value="">كل التصنيفات</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            {{-- Difficulty Filter --}}
            <select wire:model.live="difficultyFilter" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2.5 px-4 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                <option value="">كل المستويات</option>
                <option value="beginner">مبتدئ</option>
                <option value="intermediate">متوسط</option>
                <option value="expert">خبير</option>
            </select>
        </div>

        {{-- Loading indicator --}}
        <div wire:loading.class.remove="hidden" wire:loading.class="flex" class="hidden justify-center mb-4">
            <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 text-sm">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                جاري البحث...
            </div>
        </div>

        {{-- Courses Grid --}}
        @if($courses->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($courses as $course)
                    <a href="{{ route('courses.show', $course->slug) }}"
                       class="group bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">

                        {{-- Thumbnail --}}
                        <div class="aspect-video bg-gradient-to-br from-indigo-500 to-purple-600 relative overflow-hidden">
                            @if($course->getFirstMediaUrl('thumbnail'))
                                <img src="{{ $course->getFirstMediaUrl('thumbnail') }}"
                                     alt="{{ $course->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Card Body --}}
                        <div class="p-6">
                            {{-- Meta --}}
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2 text-xs font-medium">
                                    <span class="px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400">
                                        {{ $course->category?->name ?? 'عام' }}
                                    </span>
                                    <span class="text-slate-500 dark:text-slate-400">
                                        {{ match($course->difficulty->value) {
                                            'beginner' => 'مبتدئ',
                                            'intermediate' => 'متوسط',
                                            'expert' => 'خبير',
                                            default => $course->difficulty->value,
                                        } }}
                                    </span>
                                </div>
                                @if($course->price > 0)
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">${{ number_format($course->price, 2) }}</span>
                                @else
                                    <span class="text-sm font-bold text-emerald-500">مجانًا</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm leading-tight mb-2 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ $course->title }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 flex-1 mb-3">
                                {{ $course->description }}
                            </p>

                            {{-- Instructor --}}
                            <div class="flex items-center gap-2 mt-auto pt-3 border-t border-slate-100 dark:border-slate-700">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center overflow-hidden shrink-0">
                                    @if($course->instructor?->avatar)
                                        <img src="{{ asset('storage/' . $course->instructor->avatar) }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-300">{{ mb_substr($course->instructor?->name ?? 'م', 0, 1) }}</span>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-600 dark:text-slate-400 truncate">{{ $course->instructor?->name }}</span>
                                <span class="text-xs text-slate-400 ms-auto">{{ number_format($course->enrollments_count) }} طالب</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $courses->links() }}
            </div>

        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300 mb-1">لا توجد دورات</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">لم يتم العثور على دورات تطابق بحثك. جرّب كلمات أخرى.</p>
            </div>
        @endif
    </div>
</div>
