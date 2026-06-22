<x-slot name="header">
    <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
        {{ $quiz->title }}
    </h2>
</x-slot>

<div class="py-12" dir="rtl">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

        {{-- Quiz Not Started Yet --}}
        @if(!$attempt)
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl shadow-sm rounded-2xl p-8 text-center">
                <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/40 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ $quiz->title }}</h3>
                <p class="text-slate-600 dark:text-slate-400 mb-6">{{ $quiz->description }}</p>

                <div class="flex justify-center gap-6 text-sm text-slate-500 dark:text-slate-400 mb-8">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $quiz->questions->count() }} سؤال</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' دقيقة' : 'بدون وقت محدد' }}</span>
                    </div>
                </div>

                @if($quiz->is_practice)
                    <div class="bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 p-3 rounded-lg text-sm mb-6 inline-block">
                        هذا اختبار تدريبي، لن تؤثر نتيجته على تقييمك النهائي.
                    </div>
                @endif

                <button wire:click="startQuiz" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-lg shadow-indigo-600/30">
                    ابدأ الاختبار الآن
                </button>
            </div>

        {{-- Quiz Completed --}}
        @elseif($attempt->completed_at)
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl shadow-sm rounded-2xl p-10 text-center">
                {{-- Icon --}}
                <div class="w-24 h-24 {{ $attempt->is_passed ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-red-100 dark:bg-red-900/40' }} rounded-full flex items-center justify-center mx-auto mb-6">
                    @if($attempt->is_passed)
                        <svg class="w-12 h-12 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-12 h-12 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    @endif
                </div>

                <h3 class="text-3xl font-black text-slate-900 dark:text-white mb-2 flex items-center justify-center gap-3">
                    @if($attempt->is_passed)
                        أحسنت! لقد نجحت
                        <svg class="w-8 h-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    @else
                        حاول مرة أخرى
                        <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    @endif
                </h3>
                <p class="text-slate-500 dark:text-slate-400 mb-8">
                    {{ $attempt->is_passed ? 'لقد اجتزت الاختبار بنجاح وتستطيع المتابعة.' : 'لم تصل إلى درجة النجاح، يمكنك إعادة الاختبار.' }}
                </p>

                {{-- Score --}}
                <div class="inline-flex items-center gap-6 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl px-10 py-6 mb-10">
                    <div>
                        <p class="text-xs text-slate-400 mb-1 uppercase tracking-wide">النتيجة</p>
                        <p class="text-5xl font-black {{ $attempt->is_passed ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $attempt->score }}
                        </p>
                        <p class="text-xs text-slate-400 mt-1">نقطة</p>
                    </div>
                    <div class="w-px h-16 bg-slate-200 dark:bg-slate-700"></div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1 uppercase tracking-wide">الحالة</p>
                        <span class="inline-block px-4 py-1 rounded-full font-bold text-sm {{ $attempt->is_passed ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' }}">
                            {{ $attempt->is_passed ? 'ناجح' : 'راسب' }}
                        </span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-center gap-4 flex-wrap">
                    @if($attempt->is_passed && $nextStepUrl)
                        <a href="{{ $nextStepUrl }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-lg shadow-indigo-600/30">
                            {{ $nextStepLabel }}
                            <svg class="w-5 h-5 transform rotate-180 rtl:rotate-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    @elseif(!$attempt->is_passed)
                        <button wire:click="startQuiz" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-8 rounded-xl transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            إعادة الاختبار
                        </button>
                    @endif

                    @if($quiz->is_final_exam && $attempt->is_passed)
                        @php
                            $certificate = \App\Models\Certificate::where('user_id', auth()->id())->where('course_id', $quiz->course_id)->first();
                        @endphp
                        @if($certificate)
                            <a href="{{ route('certificates.download', $certificate->uuid) }}" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-lg shadow-emerald-600/30">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                تحميل الشهادة
                            </a>
                        @endif
                    @endif

                    <a href="{{ route('courses.catalog') }}" class="text-slate-500 dark:text-slate-400 hover:underline text-sm">العودة للدورات</a>
                </div>

                {{-- Detailed Review --}}
                <div class="mt-12 text-start border-t border-slate-200 dark:border-slate-700 pt-8">
                    <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-6">مراجعة الإجابات</h4>
                    <div class="space-y-6">
                        @foreach($quiz->questions as $index => $question)
                            @php
                                $studentAnswer = $attempt->answers->where('question_id', $question->id)->first();
                                $isCorrect = $studentAnswer ? $studentAnswer->is_correct : false;
                            @endphp
                            <div class="p-6 rounded-2xl border {{ $isCorrect ? 'bg-emerald-50/50 border-emerald-100 dark:bg-emerald-900/10 dark:border-emerald-900/30' : 'bg-red-50/50 border-red-100 dark:bg-red-900/10 dark:border-red-900/30' }}">
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $isCorrect ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400' }}">
                                        @if($isCorrect)
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h5 class="text-lg font-bold text-slate-900 dark:text-white mb-1">{{ $index + 1 }}. {{ $question->text }}</h5>
                                        <p class="text-sm font-bold {{ $isCorrect ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $isCorrect ? 'إجابة صحيحة' : 'إجابة خاطئة' }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="ps-12 space-y-2">
                                    @foreach($question->options as $option)
                                        @php
                                            $isSelected = $studentAnswer && $studentAnswer->question_option_id === $option->id;
                                            $isActualCorrect = $option->is_correct;
                                        @endphp
                                        <div class="flex items-center gap-3 p-3 rounded-xl border {{ $isActualCorrect ? 'bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800' : ($isSelected && !$isActualCorrect ? 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800' : 'bg-white border-slate-100 dark:bg-slate-800 dark:border-slate-700') }}">
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $isActualCorrect ? 'border-emerald-500 bg-emerald-500' : ($isSelected ? 'border-red-500 bg-red-500' : 'border-slate-300 dark:border-slate-600') }}">
                                                @if($isActualCorrect || $isSelected)
                                                    <div class="w-2 h-2 rounded-full bg-white"></div>
                                                @endif
                                            </div>
                                            <span class="{{ $isActualCorrect ? 'text-emerald-700 font-bold dark:text-emerald-400' : ($isSelected ? 'text-red-700 dark:text-red-400' : 'text-slate-600 dark:text-slate-400') }}">
                                                {{ $option->text }}
                                            </span>
                                            @if($isActualCorrect)
                                                <span class="ms-auto text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 px-2 py-1 rounded-full">الإجابة الصحيحة</span>
                                            @elseif($isSelected)
                                                <span class="ms-auto text-xs font-bold text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/40 px-2 py-1 rounded-full">إجابتك</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        {{-- Quiz In Progress --}}
        @else
            <div class="space-y-6"
                 x-data="{
                     timer: {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes * 60 : 'null' }},
                     interval: null,
                     init() {
                         if(this.timer !== null) {
                             this.interval = setInterval(() => {
                                 this.timer--;
                                 if(this.timer <= 0) {
                                     clearInterval(this.interval);
                                     $wire.timeUp();
                                 }
                             }, 1000);
                         }

                         @if($quiz->is_final_exam)
                         // Strict Anti-Cheating listeners
                         document.addEventListener('visibilitychange', () => {
                             if (document.hidden) {
                                 $wire.failExam();
                             }
                         });
                         window.addEventListener('blur', () => {
                             $wire.failExam();
                         });
                         @endif
                     },
                     formatTime() {
                         if(this.timer === null) return '--:--';
                         let m = Math.floor(this.timer / 60).toString().padStart(2, '0');
                         let s = (this.timer % 60).toString().padStart(2, '0');
                         return `${m}:${s}`;
                     }
                 }"
                 @if($quiz->is_final_exam)
                 {{-- ANTI-COPY PROTECTION --}}
                 oncopy="return false;"
                 oncut="return false;"
                 onpaste="return false;"
                 oncontextmenu="return false;"
                 class="select-none" {{-- Tailwind utility to prevent text selection --}}
                 @endif
            >
                
                {{-- Sticky Header with Timer --}}
                <div class="sticky top-20 z-40 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md shadow-sm rounded-2xl p-4 flex items-center justify-between border border-slate-100 dark:border-slate-700">
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white">{{ $quiz->title }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $quiz->questions->count() }} أسئلة</p>
                    </div>
                    
                    @if($quiz->time_limit_minutes)
                        <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-900 px-4 py-2 rounded-xl"
                             :class="timer <= 60 ? 'text-red-600 dark:text-red-400 animate-pulse' : 'text-slate-700 dark:text-slate-300'">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="font-mono font-bold text-lg" x-text="formatTime()"></span>
                        </div>
                    @endif
                </div>

                {{-- Questions --}}
                <form wire:submit="submitQuiz" class="space-y-6 pb-20">
                    @foreach($quiz->questions as $index => $question)
                        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl shadow-sm rounded-2xl p-6 border border-slate-100 dark:border-slate-700">
                            
                            <div class="flex items-start gap-4 mb-6">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center shrink-0">
                                    <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-900 dark:text-white leading-relaxed">{{ $question->text }}</h4>
                                    <span class="text-xs text-slate-400 mt-1 block">{{ $question->points }} نقطة</span>
                                </div>
                            </div>

                            <div class="ps-12 space-y-3">
                                @if($question->type->value === 'short_text')
                                    <textarea wire:model="answers.{{ $question->id }}"
                                              rows="4"
                                              class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition p-3"
                                              placeholder="اكتب إجابتك هنا..."></textarea>

                                @elseif($question->type->value === 'multiple_choice')
                                    @foreach($question->options as $option)
                                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition-colors">
                                            <input type="checkbox" wire:model="answers.{{ $question->id }}" value="{{ $option->id }}"
                                                   class="w-5 h-5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-600">
                                            <span class="text-slate-700 dark:text-slate-300">{{ $option->text }}</span>
                                        </label>
                                    @endforeach

                                @else {{-- Single choice & True/False --}}
                                    @foreach($question->options as $option)
                                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition-colors">
                                            <input type="radio" wire:model="answers.{{ $question->id }}" value="{{ $option->id }}" name="q_{{ $question->id }}"
                                                   class="w-5 h-5 text-indigo-600 border-slate-300 focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-600">
                                            <span class="text-slate-700 dark:text-slate-300">{{ $option->text }}</span>
                                        </label>
                                    @endforeach
                                @endif
                            </div>

                        </div>
                    @endforeach

                    <div class="pt-4">
                        @if($unansweredError)
                            <div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 p-4 rounded-xl border border-red-100 dark:border-red-800/50 mb-4 flex items-center gap-3">
                                <svg class="w-6 h-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>{{ $unansweredError }}</span>
                            </div>
                        @endif
                        <div class="flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-10 rounded-xl transition-colors shadow-lg shadow-indigo-600/30 text-lg">
                                تسليم الإجابات
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        @endif

    </div>
</div>
