<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ $quiz->is_final_exam ? 'إدارة الاختبار النهائي' : 'إدارة اختبار الوحدة' }}
        </h2>
    </x-slot>

    <div class="py-12" dir="rtl">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 p-4 rounded-xl border border-emerald-100 dark:border-emerald-800/50">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Settings --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm p-6 border border-slate-100 dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">إعدادات الاختبار</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">عنوان الاختبار</label>
                        <input wire:model="title" type="text" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">الوقت المحدد (بالدقائق)</label>
                        <input wire:model="timeLimit" type="number" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">وصف الاختبار</label>
                        <textarea wire:model="description" rows="3" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button wire:click="saveSettings" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors">حفظ الإعدادات</button>
                </div>
            </div>

            {{-- Questions --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm p-6 border border-slate-100 dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">الأسئلة</h3>

                <div class="space-y-6">
                    @forelse($quiz->questions as $index => $question)
                        <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-5 bg-slate-50 dark:bg-slate-900/50">
                            
                            <div class="flex justify-between items-start gap-4 mb-5">
                                <h4 class="font-bold text-slate-900 dark:text-white leading-relaxed">
                                    <span class="text-indigo-600 dark:text-indigo-400 mr-1">{{ $index + 1 }}.</span> {{ $question->text }}
                                </h4>
                                <button wire:click="deleteQuestion({{ $question->id }})" class="text-red-500 hover:text-red-700 dark:hover:text-red-400 text-sm font-medium shrink-0 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    حذف
                                </button>
                            </div>
                            
                            <div class="space-y-3 mb-5 pl-4 border-r-2 border-slate-300 dark:border-slate-600 pr-4">
                                @foreach($question->options as $option)
                                    <div class="flex items-center gap-3">
                                        <button wire:click="toggleOptionCorrectness({{ $question->id }}, {{ $option->id }})" 
                                                class="w-8 h-8 rounded-lg border flex items-center justify-center shrink-0 transition-colors {{ $option->is_correct ? 'bg-emerald-500 border-emerald-500 text-white' : 'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 text-slate-300 hover:border-emerald-500' }}">
                                            @if($option->is_correct) 
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                            @endif
                                        </button>
                                        <input type="text" wire:change="updateOptionText({{ $option->id }}, $event.target.value)" value="{{ $option->text }}" 
                                               class="flex-1 rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm py-2 focus:ring-indigo-500">
                                        <button wire:click="deleteOption({{ $option->id }})" class="text-red-500 text-xs px-2 hover:underline">حذف الخيار</button>
                                    </div>
                                @endforeach
                            </div>
                            
                            <button wire:click="addOption({{ $question->id }})" class="text-sm text-indigo-600 dark:text-indigo-400 font-bold hover:underline flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                إضافة خيار
                            </button>
                        </div>
                    @empty
                        <div class="text-center p-8 text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-dashed border-slate-300 dark:border-slate-600">
                            لا توجد أسئلة بعد، ابدأ بإضافة سؤالك الأول.
                        </div>
                    @endforelse
                </div>

                <div class="mt-8 border-t border-slate-200 dark:border-slate-700 pt-8">
                    <h4 class="font-bold text-slate-900 dark:text-white mb-4">إضافة سؤال جديد</h4>
                    <div class="flex flex-col md:flex-row gap-3">
                        <input wire:model="newQuestionText" type="text" placeholder="اكتب السؤال هنا..." 
                               class="flex-1 rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500">
                        <select wire:model="newQuestionType" class="rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500">
                            <option value="single_choice">اختيار من متعدد (إجابة واحدة)</option>
                            <option value="multiple_choice">اختيارات متعددة</option>
                            <option value="true_false">صح أو خطأ</option>
                        </select>
                        <button wire:click="addQuestion" class="bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors">إضافة السؤال</button>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex justify-center">
                <a href="{{ route('instructor.courses.builder', $quiz->course_id ?? $quiz->section->course_id) }}" class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium transition-colors">
                    <svg class="w-5 h-5 transform rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    العودة إلى لوحة الدورة
                </a>
            </div>

        </div>
    </div>
</div>
