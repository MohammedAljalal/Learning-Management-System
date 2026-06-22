<x-slot name="header">
    <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
        {{ __('لوحة الأرباح (المالية)') }}
    </h2>
</x-slot>

<div class="py-12" dir="rtl">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Available Balance --}}
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-6 text-white shadow-xl shadow-emerald-500/20 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
                <div class="relative z-10">
                    <p class="text-emerald-50 font-medium text-sm mb-1">الرصيد المتاح للسحب</p>
                    <h3 class="text-4xl font-black">${{ number_format($balance, 2) }}</h3>
                    <div class="mt-4 flex gap-2">
                        <button class="bg-white/20 hover:bg-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl backdrop-blur-md transition-colors">
                            سحب الأرباح
                        </button>
                    </div>
                </div>
            </div>

            {{-- Total Earnings --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 font-medium text-sm mb-1">إجمالي المبيعات (مدى الحياة)</p>
                        <h3 class="text-3xl font-black text-slate-900 dark:text-white">${{ number_format($totalEarnings, 2) }}</h3>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/40 flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">
                    يتم خصم 20% כعمولة منصة من كل مبيعة.
                </div>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white">المبيعات الأخيرة</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm text-slate-600 dark:text-slate-400">
                    <thead class="text-xs text-slate-500 dark:text-slate-500 bg-slate-50 dark:bg-slate-800/50 uppercase border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">الدورة</th>
                            <th scope="col" class="px-6 py-3 font-semibold">الطالب</th>
                            <th scope="col" class="px-6 py-3 font-semibold">المبلغ الكلي</th>
                            <th scope="col" class="px-6 py-3 font-semibold">عمولة المنصة</th>
                            <th scope="col" class="px-6 py-3 font-semibold text-emerald-600 dark:text-emerald-400">صافي ربحك</th>
                            <th scope="col" class="px-6 py-3 font-semibold">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                            <tr class="border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                                    {{ Str::limit($transaction->course->title, 30) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $transaction->user->name }}
                                </td>
                                <td class="px-6 py-4">
                                    ${{ number_format($transaction->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-rose-500">
                                    -${{ number_format($transaction->platform_fee, 2) }}
                                </td>
                                <td class="px-6 py-4 font-bold text-emerald-600 dark:text-emerald-400">
                                    +${{ number_format($transaction->instructor_revenue, 2) }}
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    {{ $transaction->created_at->format('Y-m-d H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    لا توجد مبيعات حتى الآن.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
