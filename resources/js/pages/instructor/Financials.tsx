import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import type { PageProps, PaginatedData } from '@/types';
import { router } from '@inertiajs/react';

interface Transaction {
    id: number;
    amount: number;
    instructor_revenue: number;
    platform_fee: number;
    payment_method: string;
    status: string;
    created_at: string;
    course_title: string;
    student_name: string;
}

interface MonthlyEarning {
    month: string;
    total: number;
}

interface Props extends PageProps {
    balance: number;
    totalEarnings: number;
    totalStudents: number;
    monthlyEarnings: MonthlyEarning[];
    transactions: PaginatedData<Transaction>;
}

const arabicMonths: Record<string, string> = {
    '01': 'يناير', '02': 'فبراير', '03': 'مارس', '04': 'أبريل',
    '05': 'مايو',  '06': 'يونيو',  '07': 'يوليو', '08': 'أغسطس',
    '09': 'سبتمبر','10': 'أكتوبر','11': 'نوفمبر','12': 'ديسمبر',
};

function formatMonth(ym: string) {
    const [year, month] = ym.split('-');
    return `${arabicMonths[month] ?? month} ${year}`;
}

export default function Financials({ balance, totalEarnings, totalStudents, monthlyEarnings, transactions }: Props) {
    const maxMonthly = Math.max(...(monthlyEarnings.map(m => m.total)), 1);

    return (
        <AppLayout>
            <Head title="الإيرادات المالية" />
            <div className="py-8 min-h-screen" dir="rtl">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                    {/* Header */}
                    <div>
                        <h1 className="text-2xl font-black text-slate-900 dark:text-white">الإيرادات المالية</h1>
                        <p className="text-slate-500 dark:text-slate-400 text-sm mt-1">ملخص أرباحك وسجل معاملاتك المالية</p>
                    </div>

                    {/* Stats Cards */}
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        {/* Balance */}
                        <div className="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-6 text-white shadow-lg shadow-indigo-600/25">
                            <div className="flex items-center gap-3 mb-3">
                                <div className="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                    <svg className="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <p className="text-white/80 text-sm font-semibold">الرصيد القابل للسحب</p>
                            </div>
                            <p className="text-4xl font-black">${balance.toFixed(2)}</p>
                            <button className="mt-4 bg-white/20 hover:bg-white/30 px-4 py-2 rounded-xl text-sm font-bold transition-colors flex items-center gap-2">
                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                طلب سحب
                            </button>
                        </div>

                        {/* Total Earnings */}
                        <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm">
                            <div className="flex items-center gap-3 mb-3">
                                <div className="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                                    <svg className="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p className="text-slate-500 dark:text-slate-400 text-sm font-semibold">إجمالي الأرباح</p>
                            </div>
                            <p className="text-4xl font-black text-slate-900 dark:text-white">${totalEarnings.toFixed(2)}</p>
                            <p className="text-xs text-slate-400 mt-2">صافي ربحك بعد عمولة المنصة</p>
                        </div>

                        {/* Total Students */}
                        <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm">
                            <div className="flex items-center gap-3 mb-3">
                                <div className="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center">
                                    <svg className="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <p className="text-slate-500 dark:text-slate-400 text-sm font-semibold">إجمالي الطلاب</p>
                            </div>
                            <p className="text-4xl font-black text-slate-900 dark:text-white">{totalStudents.toLocaleString('ar')}</p>
                            <p className="text-xs text-slate-400 mt-2">مجموع المسجلين في دوراتك</p>
                        </div>
                    </div>

                    {/* Monthly Chart */}
                    {monthlyEarnings.length > 0 && (
                        <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm">
                            <h3 className="font-bold text-slate-900 dark:text-white mb-6">الأرباح الشهرية (آخر 6 أشهر)</h3>
                            <div className="flex items-end gap-3 h-40">
                                {monthlyEarnings.map((m, i) => {
                                    const pct = (m.total / maxMonthly) * 100;
                                    return (
                                        <div key={i} className="flex-1 flex flex-col items-center gap-2">
                                            <span className="text-xs font-bold text-slate-900 dark:text-white">${m.total.toFixed(0)}</span>
                                            <div className="w-full bg-slate-100 dark:bg-slate-700 rounded-t-lg overflow-hidden" style={{ height: '80px' }}>
                                                <div
                                                    className="w-full bg-gradient-to-t from-indigo-600 to-purple-500 rounded-t-lg transition-all duration-700"
                                                    style={{ height: `${pct}%`, marginTop: `${100 - pct}%` }}
                                                />
                                            </div>
                                            <span className="text-xs text-slate-500 dark:text-slate-400 text-center leading-tight">
                                                {formatMonth(m.month)}
                                            </span>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                    {/* Transactions Table */}
                    <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm overflow-hidden">
                        <div className="p-6 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                            <h3 className="font-bold text-slate-900 dark:text-white">سجل المعاملات</h3>
                            <span className="text-sm text-slate-500 dark:text-slate-400">{transactions.total} معاملة</span>
                        </div>

                        {transactions.data.length > 0 ? (
                            <>
                                <div className="overflow-x-auto">
                                    <table className="w-full text-sm">
                                        <thead>
                                            <tr className="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                                                <th className="text-right px-6 py-4 font-semibold">التاريخ</th>
                                                <th className="text-right px-6 py-4 font-semibold">الطالب</th>
                                                <th className="text-right px-6 py-4 font-semibold">الدورة</th>
                                                <th className="text-right px-6 py-4 font-semibold">سعر الدورة</th>
                                                <th className="text-right px-6 py-4 font-semibold">عمولة المنصة</th>
                                                <th className="text-right px-6 py-4 font-semibold">صافي ربحك</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-slate-100 dark:divide-slate-700">
                                            {transactions.data.map(tx => (
                                                <tr key={tx.id} className="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                                    <td className="px-6 py-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                                        {new Date(tx.created_at).toLocaleDateString('ar')}
                                                    </td>
                                                    <td className="px-6 py-4 font-medium text-slate-900 dark:text-slate-200">
                                                        {tx.student_name}
                                                    </td>
                                                    <td className="px-6 py-4 text-slate-700 dark:text-slate-300 max-w-[200px] truncate">
                                                        {tx.course_title}
                                                    </td>
                                                    <td className="px-6 py-4 font-semibold text-slate-900 dark:text-white">
                                                        ${Number(tx.amount).toFixed(2)}
                                                    </td>
                                                    <td className="px-6 py-4 text-red-500 dark:text-red-400 font-semibold">
                                                        -${Number(tx.platform_fee).toFixed(2)}
                                                    </td>
                                                    <td className="px-6 py-4 font-black text-emerald-600 dark:text-emerald-400">
                                                        +${Number(tx.instructor_revenue).toFixed(2)}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>

                                {/* Pagination */}
                                {transactions.last_page > 1 && (
                                    <div className="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-center gap-2">
                                        {transactions.links.map((link, i) => (
                                            <button
                                                key={i}
                                                disabled={!link.url}
                                                onClick={() => link.url && router.visit(link.url)}
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                                className={`px-3 py-1.5 rounded-lg text-sm font-semibold transition-all ${
                                                    link.active
                                                        ? 'bg-indigo-600 text-white'
                                                        : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 disabled:opacity-40 disabled:cursor-not-allowed'
                                                }`}
                                            />
                                        ))}
                                    </div>
                                )}
                            </>
                        ) : (
                            <div className="flex flex-col items-center justify-center py-20 text-center">
                                <div className="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                                    <svg className="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <p className="font-semibold text-slate-600 dark:text-slate-400">لا توجد معاملات مالية بعد</p>
                                <p className="text-sm text-slate-400 mt-1">ستظهر هنا عند اشتراك الطلاب في دوراتك المدفوعة</p>
                            </div>
                        )}
                    </div>

                </div>
            </div>
        </AppLayout>
    );
}
