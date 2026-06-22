<x-slot name="header">
    <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">دوراتي التدريبية</h2>
</x-slot>

<div class="py-8" dir="rtl">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 rounded-xl px-4 py-3 text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-slate-900 dark:text-white text-lg">دوراتي ({{ $courses->total() }})</h3>
                <button wire:click="openCreate"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm px-4 py-2 rounded-xl transition-colors shadow-lg shadow-indigo-600/20">
                    + دورة جديدة
                </button>
            </div>

            @forelse($courses as $course)
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-5 border-b border-slate-100 dark:border-slate-700 last:border-0 hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h4 class="font-bold text-slate-900 dark:text-white text-base truncate">{{ $course->title }}</h4>
                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                                {{ $course->status->value === 'published' ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400' }}">
                                {{ $course->status->value === 'published' ? 'منشور' : 'مسودة' }}
                            </span>
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $course->category?->name }} · {{ $course->sections_count }} وحدة · {{ $course->enrollments_count }} طالب</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('instructor.courses.builder', $course) }}"
                           class="text-xs font-bold px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                            المحتوى
                        </a>
                        <button wire:click="openEdit({{ $course->id }})"
                                class="text-xs font-bold px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                            تعديل
                        </button>
                        <button wire:click="toggleStatus({{ $course->id }})"
                                class="text-xs font-bold px-3 py-2 rounded-lg transition-colors
                                    {{ $course->status->value === 'published' ? 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 hover:bg-amber-200' : 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-200' }}">
                            {{ $course->status->value === 'published' ? 'إخفاء' : 'نشر' }}
                        </button>
                        <button wire:click="delete({{ $course->id }})"
                                wire:confirm="هل أنت متأكد من حذف هذه الدورة؟"
                                class="text-xs font-bold px-3 py-2 rounded-lg bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 hover:bg-red-200 transition-colors">
                            حذف
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-16 text-center">
                    <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">لم تنشئ أي دورة بعد. ابدأ الآن!</p>
                </div>
            @endforelse

            @if($courses->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">{{ $courses->links() }}</div>
            @endif
        </div>

    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ $editingId ? 'تعديل الدورة' : 'دورة جديدة' }}
                    </h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">عنوان الدورة <span class="text-red-500">*</span></label>
                        <input wire:model="title" type="text" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">الوصف <span class="text-red-500">*</span></label>
                        <textarea wire:model="description" rows="4" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"></textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">التصنيف <span class="text-red-500">*</span></label>
                            <select wire:model="categoryId" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                                <option value="">اختر...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('categoryId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">السعر ($) <span class="text-red-500">*</span></label>
                            <input wire:model="price" type="number" step="0.01" min="0" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">المستوى</label>
                            <select wire:model="difficulty" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                                <option value="beginner">مبتدئ</option>
                                <option value="intermediate">متوسط</option>
                                <option value="expert">خبير</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="closeModal" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition">إلغاء</button>
                    <button wire:click="save" class="px-6 py-2 rounded-xl text-sm font-bold bg-indigo-600 hover:bg-indigo-500 text-white transition shadow-lg shadow-indigo-600/20">
                        {{ $editingId ? 'حفظ التعديلات' : 'إنشاء الدورة' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
