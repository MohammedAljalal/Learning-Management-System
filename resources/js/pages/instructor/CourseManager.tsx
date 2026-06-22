import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';
import type { PageProps, Course, PaginatedData } from '@/types';

interface Props extends PageProps {
    courses: PaginatedData<Course>;
}

const statusColors = {
    draft: 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400',
    published: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
    archived: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
};

const difficultyMap = {
    beginner: 'مبتدئ',
    intermediate: 'متوسط',
    expert: 'خبير',
};

export default function CourseManager({ courses }: Props) {
    const [deleting, setDeleting] = useState<number | null>(null);

    const deleteCourse = (id: number) => {
        if (!confirm('هل أنت متأكد من حذف هذه الدورة؟')) return;
        setDeleting(id);
        router.delete(`/instructor/courses/${id}`, {
            onFinish: () => setDeleting(null),
        });
    };

    return (
        <AppLayout>
            <Head title="إدارة الدورات" />
            <div className="py-8 min-h-screen" dir="rtl">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">

                    {/* Header */}
                    <div className="flex items-center justify-between mb-8">
                        <div>
                            <h1 className="text-2xl font-black text-slate-900 dark:text-white">دوراتي</h1>
                            <p className="text-slate-500 dark:text-slate-400 text-sm mt-1">{courses.total} دورة</p>
                        </div>
                        <Link href="/instructor/courses/create"
                            className="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-indigo-600/25">
                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                            </svg>
                            دورة جديدة
                        </Link>
                    </div>

                    {/* Table */}
                    {courses.data.length > 0 ? (
                        <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm overflow-hidden">
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80">
                                            <th className="text-right px-6 py-4 font-semibold text-slate-500 dark:text-slate-400">الدورة</th>
                                            <th className="text-right px-4 py-4 font-semibold text-slate-500 dark:text-slate-400">الحالة</th>
                                            <th className="text-right px-4 py-4 font-semibold text-slate-500 dark:text-slate-400">المستوى</th>
                                            <th className="text-right px-4 py-4 font-semibold text-slate-500 dark:text-slate-400">السعر</th>
                                            <th className="text-right px-4 py-4 font-semibold text-slate-500 dark:text-slate-400">الطلاب</th>
                                            <th className="text-right px-4 py-4 font-semibold text-slate-500 dark:text-slate-400">الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100 dark:divide-slate-700">
                                        {courses.data.map(course => {
                                            const statusLabel: Record<string, { label: string; color: string }> = {
                                                draft: { label: 'مسودة', color: 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400' },
                                                published: { label: 'منشورة', color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
                                                archived: { label: 'مؤرشفة', color: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' },
                                            };
                                            const currentStatus = statusLabel[(course as any).status] || statusLabel.draft;

                                            return (
                                            <tr key={course.id} className="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className="w-12 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shrink-0 overflow-hidden">
                                                            {course.thumbnail_url && (
                                                                <img src={course.thumbnail_url} alt={course.title} className="w-full h-full object-cover" />
                                                            )}
                                                        </div>
                                                        <div className="min-w-0">
                                                            <p className="font-semibold text-slate-900 dark:text-white truncate max-w-xs">{course.title}</p>
                                                            <p className="text-xs text-slate-400 mt-0.5">{course.category?.name ?? 'غير محدد'}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-4 py-4">
                                                    <span className={`px-2 py-1 text-xs font-bold rounded-lg ${currentStatus.color}`}>
                                                        {currentStatus.label}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-4">
                                                    <span className="text-xs font-medium text-slate-600 dark:text-slate-400">
                                                        {difficultyMap[course.difficulty] ?? course.difficulty}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-4 font-semibold text-slate-900 dark:text-white">
                                                    {course.price > 0 ? `$${Number(course.price).toFixed(2)}` : <span className="text-emerald-600 dark:text-emerald-400">مجاني</span>}
                                                </td>
                                                <td className="px-4 py-4 text-slate-600 dark:text-slate-400">
                                                    {course.enrollments_count?.toLocaleString('ar') ?? 0}
                                                </td>
                                                <td className="px-4 py-4">
                                                    <div className="flex items-center gap-2">
                                                        <Link href={`/instructor/courses/${course.id}/builder`}
                                                            className="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                            منشئ الدورة
                                                        </Link>
                                                        <span className="text-slate-300 dark:text-slate-600">|</span>
                                                        <button 
                                                            onClick={() => deleteCourse(course.id)}
                                                            disabled={deleting === course.id}
                                                            className="text-xs font-bold text-red-500 hover:text-red-600 dark:hover:text-red-400 hover:underline disabled:opacity-50 transition-colors">
                                                            {deleting === course.id ? 'جاري الحذف...' : 'حذف'}
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    ) : (
                        <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-16 text-center shadow-sm">
                            <div className="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                                <svg className="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h3 className="text-lg font-bold text-slate-700 dark:text-slate-300 mb-2">لا توجد دورات بعد</h3>
                            <p className="text-slate-500 dark:text-slate-400 text-sm mb-6">ابدأ بإنشاء دورتك التدريبية الأولى</p>
                            <Link href="/instructor/courses/create"
                                className="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-6 py-3 rounded-xl transition-all">
                                إنشاء دورة
                            </Link>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
