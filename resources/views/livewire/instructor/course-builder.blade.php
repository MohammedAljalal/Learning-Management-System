<x-slot name="header">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            محتوى الدورة: {{ $course->title }}
        </h2>
        <a href="{{ route('instructor.courses') }}" class="text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
            &larr; العودة
        </a>
    </div>
</x-slot>

<div class="py-8" dir="rtl">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 rounded-xl px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">الوحدات والدروس</h3>
            <button wire:click="openSectionModal()"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm px-4 py-2 rounded-xl transition-colors shadow-lg shadow-indigo-600/20">
                + وحدة جديدة
            </button>
        </div>

        @forelse($course->sections as $section)
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm overflow-hidden border border-slate-200 dark:border-slate-700">
                {{-- Section Header --}}
                <div class="bg-slate-50 dark:bg-slate-900/50 p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-base">الوحدة {{ $section->order }}: {{ $section->title }}</h4>
                        @if($section->description)
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $section->description }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="openLessonModal({{ $section->id }})" class="text-xs font-bold px-3 py-1.5 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200 transition-colors">
                            + درس
                        </button>
                        <button wire:click="createSectionQuiz({{ $section->id }})" class="text-xs font-bold px-3 py-1.5 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 hover:bg-amber-200 transition-colors">
                            + اختبار
                        </button>
                        <button wire:click="openSectionModal({{ $section->id }})" class="text-xs font-bold px-3 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300 transition-colors">
                            تعديل
                        </button>
                        <button wire:click="deleteSection({{ $section->id }})" wire:confirm="هل أنت متأكد من حذف الوحدة بجميع دروسها؟" class="text-xs font-bold px-3 py-1.5 rounded-lg bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 hover:bg-red-200 transition-colors">
                            حذف
                        </button>
                    </div>
                </div>

                {{-- Lessons List --}}
                <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @forelse($section->lessons as $lesson)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors pl-8">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs shrink-0">
                                    {{ $lesson->order }}
                                </div>
                                <div>
                                    <h5 class="font-medium text-slate-800 dark:text-slate-200 text-sm">{{ $lesson->title }}</h5>
                                    @if($lesson->hasMedia('lesson_video'))
                                        <span class="inline-flex mt-1 items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            فيديو
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="openLessonModal({{ $section->id }}, {{ $lesson->id }})" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">تعديل</button>
                                <button wire:click="deleteLesson({{ $lesson->id }})" wire:confirm="هل أنت متأكد من حذف الدرس؟" class="text-xs text-red-600 dark:text-red-400 hover:underline font-medium">حذف</button>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">لا توجد دروس في هذه الوحدة.</div>
                    @endforelse

                    @php
                        $quiz = \App\Models\Quiz::where('section_id', $section->id)->first();
                    @endphp
                    @if($quiz)
                        <div class="p-4 flex items-center justify-between bg-amber-50/50 dark:bg-amber-900/10 transition-colors pl-8">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 font-bold text-xs shrink-0">
                                    Q
                                </div>
                                <div>
                                    <h5 class="font-medium text-slate-800 dark:text-slate-200 text-sm">{{ $quiz->title }}</h5>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('instructor.quizzes.manage', $quiz->id) }}" class="text-xs text-amber-600 dark:text-amber-400 hover:underline font-medium">إدارة الاختبار</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-16 text-center bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <p class="text-sm text-slate-500 dark:text-slate-400">الدورة لا تحتوي على أي وحدات بعد. ابدأ بإضافة وحدة جديدة.</p>
            </div>
        @endforelse

        <div class="mt-8 flex justify-center">
            <button wire:click="createFinalExam" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-xl font-bold transition-colors shadow-lg shadow-indigo-600/30 text-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                إدارة الاختبار النهائي للدورة
            </button>
        </div>

    </div>

    {{-- Section Modal --}}
    @if($showSectionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
                    {{ $editingSectionId ? 'تعديل الوحدة' : 'وحدة جديدة' }}
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">عنوان الوحدة</label>
                        <input wire:model="sectionTitle" type="text" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        @error('sectionTitle') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">الوصف</label>
                        <textarea wire:model="sectionDescription" rows="3" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2 focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">الترتيب</label>
                        <input wire:model="sectionOrder" type="number" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showSectionModal', false)" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600">إلغاء</button>
                    <button wire:click="saveSection" class="px-6 py-2 rounded-xl text-sm font-bold bg-indigo-600 text-white hover:bg-indigo-500">حفظ</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Lesson Modal --}}
    @if($showLessonModal)
        <div x-data="{ isUploading: false, progress: 0 }"
             x-on:livewire-upload-start="isUploading = true"
             x-on:livewire-upload-finish="isUploading = false"
             x-on:livewire-upload-error="isUploading = false"
             x-on:livewire-upload-progress="progress = $event.detail.progress"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
                    {{ $editingLessonId ? 'تعديل الدرس' : 'درس جديد' }}
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">عنوان الدرس</label>
                        <input wire:model="lessonTitle" type="text" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        @error('lessonTitle') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">محتوى نصي (اختياري)</label>
                        <textarea wire:model="lessonContent" rows="4" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2 focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">الترتيب</label>
                            <input wire:model="lessonOrder" type="number" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">فيديو (MP4)</label>
                            <input wire:model="videoFile" type="file" accept="video/mp4,video/x-m4v,video/*" class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
                            
                            <div x-show="isUploading" style="display: none;" class="mt-2">
                                <div class="w-full bg-slate-200 rounded-full h-1.5 dark:bg-slate-700 overflow-hidden">
                                    <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300" :style="`width: ${progress}%`"></div>
                                </div>
                                <div class="text-xs text-indigo-500 mt-1 font-medium text-left" x-text="`${progress}%`"></div>
                            </div>
                            @error('videoFile') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showLessonModal', false)" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600">إلغاء</button>
                    <button wire:click="saveLesson" x-bind:disabled="isUploading" :class="isUploading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-500'" class="px-6 py-2 rounded-xl text-sm font-bold bg-indigo-600 text-white">حفظ</button>
                </div>
            </div>
        </div>
    @endif
</div>
