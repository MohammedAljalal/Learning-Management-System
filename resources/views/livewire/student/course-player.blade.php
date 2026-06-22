<x-slot name="header">
    <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight truncate">
        {{ $course->title }}
    </h2>
</x-slot>

<div class="py-8" dir="rtl"
    x-data="{
        currentTime: 0,
        savedPosition: {{ $savedPosition }},
        saveInterval: null,
        interactiveQuestions: @js($interactiveQuestions),
        activeQuestion: null,

        initPlayer(videoEl) {
            if (!videoEl) return;
            // Restore saved position
            if (this.savedPosition > 0) {
                videoEl.currentTime = this.savedPosition;
            }
            // Track position every second while playing
            videoEl.addEventListener('play', () => {
                this.saveInterval = setInterval(() => {
                    this.currentTime = Math.floor(videoEl.currentTime);
                    
                    // Check for interactive questions
                    const question = this.interactiveQuestions.find(q => q.timestamp === this.currentTime && !q.answered);
                    if (question) {
                        videoEl.pause();
                        this.activeQuestion = question;
                    }

                    if (this.currentTime % 5 === 0) {
                        $wire.saveProgress(this.currentTime);
                    }
                }, 1000);
            });
            videoEl.addEventListener('pause', () => {
                clearInterval(this.saveInterval);
                this.currentTime = Math.floor(videoEl.currentTime);
                $wire.saveProgress(this.currentTime);
            });
            videoEl.addEventListener('ended', () => {
                clearInterval(this.saveInterval);
                $wire.markCompleted();
            });
        },
        answerQuestion(optionId) {
            // For now, just resume regardless of answer correctness, or you can validate here
            this.activeQuestion.answered = true;
            this.activeQuestion = null;
            $refs.player.play();
        }
    }">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Video Player Column --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Player Container --}}
                <div class="bg-black rounded-2xl overflow-hidden shadow-2xl relative"
                     oncontextmenu="return false;">

                    @if($videoUrl)
                        <video
                            x-ref="player"
                            x-init="initPlayer($refs.player)"
                            class="w-full aspect-video"
                            controls
                            controlslist="nodownload nofullscreen"
                            disablePictureInPicture
                            preload="metadata"
                        >
                            <source src="{{ $videoUrl }}" type="video/mp4">
                            <p class="text-white text-center p-4">المتصفح لا يدعم تشغيل الفيديو.</p>
                        </video>
                    @else
                        {{-- Graceful Fallback: No video uploaded yet --}}
                        <div class="aspect-video flex flex-col items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900 text-center p-8">
                            <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-white/80 font-semibold mb-1">لا يوجد فيديو لهذا الدرس بعد</p>
                            <p class="text-white/40 text-sm">يقوم المدرب بتحميل المحتوى قريباً</p>
                        </div>
                    @endif

                    {{-- Interactive Question Overlay --}}
                    <div x-show="activeQuestion" style="display: none;" class="absolute inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-6">
                        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-8 max-w-lg w-full text-right" @click.stop>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6" x-text="activeQuestion?.text"></h3>
                            
                            <div class="space-y-3">
                                <template x-for="option in activeQuestion?.options" :key="option.id">
                                    <button @click="answerQuestion(option.id)" 
                                            class="w-full text-right p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:border-indigo-500 transition-colors text-slate-700 dark:text-slate-300">
                                        <span x-text="option.text"></span>
                                    </button>
                                </template>
                            </div>
                            <p class="text-xs text-slate-500 mt-6 text-center">أجب على السؤال لمتابعة الفيديو</p>
                        </div>
                    </div>
                </div>

                {{-- Lesson Info --}}
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm p-6">
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $lesson->title }}</h1>
                    @if($lesson->content)
                        <div class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed prose prose-sm dark:prose-invert max-w-none">
                            {!! nl2br(e($lesson->content)) !!}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Course Curriculum Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm overflow-hidden sticky top-4">
                    <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm">محتوى الدورة</h3>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-[70vh] overflow-y-auto">
                        @foreach($course->sections as $section)
                            <div>
                                {{-- Section Header --}}
                                <div class="px-4 py-3 bg-slate-50 dark:bg-slate-700/50">
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">{{ $section->title }}</p>
                                </div>

                                {{-- Lessons --}}
                                @foreach($section->lessons as $sectionLesson)
                                    @php
                                        $isUnlocked = in_array($sectionLesson->id, $unlockedLessonIds);
                                        $isActive = $sectionLesson->id === $lesson->id;
                                    @endphp

                                    @if($isUnlocked)
                                        <a href="{{ route('courses.learn', [$course->slug, $sectionLesson->id]) }}"
                                           class="flex items-center gap-3 px-4 py-3 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors {{ $isActive ? 'bg-indigo-50 dark:bg-indigo-900/30 border-s-2 border-indigo-500' : '' }}">
                                            <div class="w-6 h-6 rounded-full shrink-0 flex items-center justify-center {{ $isActive ? 'bg-indigo-500 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-400' }}">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            </div>
                                            <span class="text-xs text-slate-700 dark:text-slate-300 leading-tight">{{ $sectionLesson->title }}</span>
                                        </a>
                                    @else
                                        <div class="flex items-center gap-3 px-4 py-3 opacity-50 cursor-not-allowed">
                                            <div class="w-6 h-6 rounded-full shrink-0 flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-400">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            </div>
                                            <span class="text-xs text-slate-500 dark:text-slate-400 leading-tight">{{ $sectionLesson->title }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    {{-- Certificate Download --}}
                    <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                        @php
                            $certificate = \App\Models\Certificate::where('user_id', auth()->id())->where('course_id', $course->id)->first();
                        @endphp
                        @if($certificate)
                            <a href="{{ route('certificates.download', $certificate->uuid) }}" target="_blank" class="w-full inline-flex justify-center items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-xl transition-colors shadow-sm text-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                تحميل الشهادة
                            </a>
                        @else
                            <p class="text-xs text-center text-slate-500 dark:text-slate-400">أكمل الدورة والاختبار النهائي للحصول على الشهادة.</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- AI Chatbot Widget --}}
    <livewire:student.ai-chatbot :course="$course" :lesson="$lesson" />
</div>
