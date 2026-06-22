<x-slot name="header">
    <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">إدارة التصنيفات</h2>
</x-slot>

<div class="py-8" dir="rtl">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

        {{-- Flash Message --}}
        @if(session('success'))
            <div class="mb-4 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 rounded-xl px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between p-6 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-slate-900 dark:text-white text-lg">التصنيفات ({{ $categories->total() }})</h3>
                <button wire:click="openCreate"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm px-4 py-2 rounded-xl transition-colors shadow-lg shadow-indigo-600/20">
                    + تصنيف جديد
                </button>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/30">
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">الاسم</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">الأيقونة</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">الأب</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">الدورات</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($categories as $category)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $category->icon ?? '—' }}</td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $category->parent?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400">{{ $category->courses_count }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="openEdit({{ $category->id }})" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">تعديل</button>
                                        <button wire:click="delete({{ $category->id }})"
                                                wire:confirm="هل أنت متأكد من حذف هذا التصنيف؟"
                                                class="text-xs text-red-600 dark:text-red-400 hover:underline font-medium">حذف</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">لا توجد تصنيفات بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ $editingId ? 'تعديل التصنيف' : 'تصنيف جديد' }}
                    </h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">اسم التصنيف <span class="text-red-500">*</span></label>
                        <input wire:model="name" type="text"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">الأيقونة (Emoji أو نص)</label>
                        <input wire:model="icon" type="text"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">التصنيف الأب (اختياري)</label>
                        <select wire:model="parentId"
                                class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            <option value="">— بلا أب —</option>
                            @foreach($parentOptions as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="closeModal" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        إلغاء
                    </button>
                    <button wire:click="save" class="px-6 py-2 rounded-xl text-sm font-bold bg-indigo-600 hover:bg-indigo-500 text-white transition shadow-lg shadow-indigo-600/20">
                        {{ $editingId ? 'حفظ التعديلات' : 'إنشاء التصنيف' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
