<x-slot name="header">
    <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
        أهلاً، {{ auth()->user()->name }} 
    </h2>
</x-slot>

<div class="py-8" dir="rtl">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        {{-- ── XP & Level Banner ─────────────────────── --}}
        <div class="bg-gradient-to-l from-indigo-600 to-purple-700 rounded-2xl p-6 text-white shadow-xl shadow-indigo-600/20 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                {{-- Avatar / Level Badge --}}
                <div class="relative">
                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-black">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="absolute -bottom-1 -end-1 bg-amber-400 text-slate-900 text-xs font-black px-1.5 py-0.5 rounded-full">
                        {{ $level }}
                    </div>
                </div>
                <div>
                    <p class="text-white/70 text-sm">المستوى الحالي</p>
                    <h3 class="text-2xl font-black">المستوى {{ $level }}</h3>
                    <p class="text-white/60 text-xs mt-0.5">{{ number_format($totalXp) }} XP إجمالي</p>
                </div>
            </div>
            {{-- XP Progress Bar --}}
            <div class="w-full sm:w-72">
                <div class="flex justify-between text-xs text-white/70 mb-1.5">
                    <span>تقدم للمستوى {{ $level + 1 }}</span>
                    <span>{{ $xpForNextLevel }} XP متبقية</span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-3 overflow-hidden">
                    <div class="bg-amber-400 h-3 rounded-full transition-all duration-700" style="width: {{ $levelProgress }}%"></div>
                </div>
                <p class="text-xs text-white/50 mt-1 text-center">{{ $levelProgress }}٪</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── Left Column: Courses ─────────────────── --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- My Enrolled Courses --}}
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-bold text-slate-900 dark:text-white">دوراتي المسجّلة</h3>
                        <a href="{{ route('courses.catalog') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">استعرض المزيد</a>
                    </div>

                    @if($enrollments->count() > 0)
                        <div class="space-y-4">
                            @foreach($enrollments as $enrollment)
                                @php $course = $enrollment->course; $pct = $progress[$course->id] ?? 0; @endphp
                                <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors group">
                                    {{-- Thumbnail --}}
                                    <div class="w-16 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shrink-0 overflow-hidden">
                                        @if($course->getFirstMediaUrl('thumbnail'))
                                            <img src="{{ $course->getFirstMediaUrl('thumbnail') }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            {{ $course->title }}
                                        </h4>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $course->instructor?->name }}</p>
                                        <div class="flex items-center gap-2 mt-1.5">
                                            <div class="flex-1 bg-slate-200 dark:bg-slate-700 rounded-full h-1.5">
                                                <div class="bg-indigo-500 h-1.5 rounded-full" style="width:{{ $pct }}%"></div>
                                            </div>
                                            <span class="text-xs text-slate-500 dark:text-slate-400 shrink-0">{{ $pct }}٪</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('courses.show', $course->slug) }}"
                                       class="shrink-0 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors">
                                        {{ $pct > 0 ? 'متابعة' : 'ابدأ' }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">لم تسجّل في أي دورة بعد.</p>
                            <a href="{{ route('courses.catalog') }}" class="mt-3 inline-block text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">استعرض الدورات</a>
                        </div>
                    @endif
                </div>

                {{-- Certificates --}}
                @if($certificates->count() > 0)
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm p-6">
                        <h3 class="font-bold text-slate-900 dark:text-white mb-5">شهاداتي</h3>
                        <div class="space-y-3">
                            @foreach($certificates as $cert)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-amber-500 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $cert->course?->title }}</p>
                                            <p class="text-xs text-slate-500">{{ $cert->issued_at->format('Y-m-d') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('certificates.download', $cert) }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">تحميل</a>
                                        <a href="{{ route('certificates.verify', $cert->uuid) }}" target="_blank" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">تحقق</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- ── Right Column: XP & Notifications ──────── --}}
            <div class="space-y-6">

                {{-- Recent XP --}}
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-4">آخر نقاط XP المكتسبة</h3>
                    @if($recentXp->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentXp as $tx)
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-slate-700 dark:text-slate-300 leading-snug">{{ $tx->description }}</p>
                                    <span class="text-sm font-black text-emerald-600 dark:text-emerald-400 shrink-0 ms-2">+{{ $tx->amount }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-4">لم تكتسب نقاط بعد.</p>
                    @endif
                </div>

                {{-- Recent Notifications --}}
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-4">الإشعارات الأخيرة</h3>
                    @if($recentNotifications->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentNotifications as $notif)
                                <div class="flex items-start gap-3 {{ is_null($notif->read_at) ? 'opacity-100' : 'opacity-60' }}">
                                    <div class="w-2 h-2 rounded-full mt-2 shrink-0 {{ is_null($notif->read_at) ? 'bg-indigo-500' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                                    <div>
                                        <p class="text-sm text-slate-700 dark:text-slate-300">{{ $notif->data['message'] ?? 'إشعار جديد' }}</p>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-4">لا توجد إشعارات.</p>
                    @endif
                </div>

            </div>

        </div>

    </div>
</div>
