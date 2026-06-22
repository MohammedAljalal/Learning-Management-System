import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import type { PageProps, Course } from '@/types';

interface Stats {
    totalCourses: number;
    publishedCourses: number;
    draftCourses: number;
    totalStudents: number;
    totalEarnings: number;
}

interface RecentEnrollment {
    id: number;
    enrolled_at: string;
    user: { id: number; name: string };
    course: { id: number; title: string };
}

interface Props extends PageProps {
    stats: Stats;
    courses: Course[];
    recentEnrollments: RecentEnrollment[];
}

const statusMap: Record<string, { label: string; color: string }> = {
    draft:     { label: 'مسودة',   color: 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300' },
    published: { label: 'منشورة',  color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' },
    archived:  { label: 'مؤرشفة', color: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400' },
};

const difficultyMap: Record<string, string> = {
    beginner:     'مبتدئ',
    intermediate: 'متوسط',
    expert:       'خبير',
};

export default function InstructorDashboard({ stats, courses, recentEnrollments, auth }: Props) {
    const userName = auth?.user?.name ?? 'المدرب';

    const statCards = [
        {
            label: 'إجمالي الدورات',
            value: stats.totalCourses,
            icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            color: 'from-indigo-500 to-purple-600',
            shadow: 'shadow-indigo-500/25',
            bg: 'bg-indigo-50 dark:bg-indigo-900/20',
            text: 'text-indigo-600 dark:text-indigo-400',
        },
        {
            label: 'الدورات المنشورة',
            value: stats.publishedCourses,
            icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            color: 'from-emerald-500 to-teal-600',
            shadow: 'shadow-emerald-500/25',
            bg: 'bg-emerald-50 dark:bg-emerald-900/20',
            text: 'text-emerald-600 dark:text-emerald-400',
        },
        {
            label: 'إجمالي الطلاب',
            value: stats.totalStudents,
            icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            color: 'from-sky-500 to-blue-600',
            shadow: 'shadow-sky-500/25',
            bg: 'bg-sky-50 dark:bg-sky-900/20',
            text: 'text-sky-600 dark:text-sky-400',
        },
        {
            label: 'إجمالي الأرباح',
            value: `$${stats.totalEarnings.toFixed(2)}`,
            icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            color: 'from-amber-500 to-orange-600',
            shadow: 'shadow-amber-500/25',
            bg: 'bg-amber-50 dark:bg-amber-900/20',
            text: 'text-amber-600 dark:text-amber-400',
        },
    ];

    return (
        <AppLayout>
            <Head title="لوحة تحكم المدرب" />
            <div className="py-8 min-h-screen" dir="rtl">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                    {/* Header */}
                    <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h1 className="flex items-center gap-2 text-3xl font-black text-slate-900 dark:text-white">
                                <span>أهلاً، {userName}</span>
                                <svg className="w-7 h-7 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </h1>
                            <p className="text-slate-500 dark:text-slate-400 mt-1 text-sm">
                                هنا ملخص لأداء دوراتك وآخر نشاط الطلاب
                            </p>
                        </div>
                        <div className="flex items-center gap-3">
                            <Link
                                href="/instructor/courses"
                                className="inline-flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-semibold px-4 py-2.5 rounded-xl text-sm hover:border-indigo-400 transition-all shadow-sm"
                            >
                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                                إدارة الدورات
                            </Link>
                            <Link
                                href="/instructor/courses/create"
                                className="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2.5 rounded-xl text-sm transition-all shadow-lg shadow-indigo-600/25 hover:-translate-y-0.5"
                            >
                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                                </svg>
                                دورة جديدة
                            </Link>
                        </div>
                    </div>

                    {/* Stats Grid */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        {statCards.map((card, i) => (
                            <div
                                key={i}
                                className={`bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm hover:shadow-lg transition-shadow`}
                            >
                                <div className="flex items-center justify-between mb-4">
                                    <div className={`w-11 h-11 rounded-xl ${card.bg} flex items-center justify-center`}>
                                        <svg className={`w-6 h-6 ${card.text}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d={card.icon} />
                                        </svg>
                                    </div>
                                    <div className={`w-2 h-2 rounded-full bg-gradient-to-br ${card.color} shadow-lg ${card.shadow}`} />
                                </div>
                                <p className="text-3xl font-black text-slate-900 dark:text-white">{card.value}</p>
                                <p className="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">{card.label}</p>
                            </div>
                        ))}
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        {/* Courses List */}
                        <div className="lg:col-span-2">
                            <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm overflow-hidden">
                                <div className="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                                    <h2 className="font-bold text-slate-900 dark:text-white text-base">دوراتي</h2>
                                    <Link href="/instructor/courses" className="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-semibold">
                                        عرض الكل
                                    </Link>
                                </div>

                                {courses.length > 0 ? (
                                    <div className="divide-y divide-slate-100 dark:divide-slate-700">
                                        {courses.slice(0, 6).map(course => {
                                            const status = statusMap[(course as any).status] ?? statusMap.draft;
                                            return (
                                                <div key={course.id} className="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                                    {/* Thumbnail */}
                                                    <div className="w-14 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shrink-0 overflow-hidden">
                                                        {course.thumbnail_url && (
                                                            <img src={course.thumbnail_url} alt={course.title} className="w-full h-full object-cover" />
                                                        )}
                                                    </div>
                                                    {/* Info */}
                                                    <div className="flex-1 min-w-0">
                                                        <p className="font-bold text-slate-900 dark:text-white text-sm truncate">{course.title}</p>
                                                        <div className="flex items-center gap-2 mt-1">
                                                            <span className={`text-xs font-bold px-2 py-0.5 rounded-lg ${status.color}`}>
                                                                {status.label}
                                                            </span>
                                                            <span className="text-xs text-slate-400">
                                                                {difficultyMap[course.difficulty] ?? course.difficulty}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    {/* Students */}
                                                    <div className="text-center shrink-0">
                                                        <p className="text-base font-black text-slate-900 dark:text-white">{(course as any).enrollments_count ?? 0}</p>
                                                        <p className="text-xs text-slate-400">طالب</p>
                                                    </div>
                                                    {/* Action */}
                                                    <Link
                                                        href={`/instructor/courses/${course.id}/builder`}
                                                        className="shrink-0 text-xs font-bold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 px-3 py-1.5 rounded-lg transition-colors"
                                                    >
                                                        تعديل
                                                    </Link>
                                                </div>
                                            );
                                        })}
                                    </div>
                                ) : (
                                    <div className="flex flex-col items-center justify-center py-16 text-center px-6">
                                        <div className="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                                            <svg className="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <p className="text-slate-600 dark:text-slate-400 font-semibold mb-1">لم تقم بإنشاء أي دورة بعد</p>
                                        <p className="text-sm text-slate-400 mb-4">أنشئ أول دورة لك وابدأ في نشر المعرفة</p>
                                        <Link
                                            href="/instructor/courses/create"
                                            className="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2 rounded-xl text-sm transition-all"
                                        >
                                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                                            </svg>
                                            إنشاء دورة جديدة
                                        </Link>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Recent Enrollments */}
                        <div>
                            <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm overflow-hidden">
                                <div className="px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                                    <h2 className="font-bold text-slate-900 dark:text-white text-base">آخر التسجيلات</h2>
                                </div>

                                {recentEnrollments.length > 0 ? (
                                    <div className="divide-y divide-slate-100 dark:divide-slate-700">
                                        {recentEnrollments.map(enrollment => (
                                            <div key={enrollment.id} className="flex items-start gap-3 px-5 py-3.5">
                                                <div className="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shrink-0 text-white text-xs font-black">
                                                    {enrollment.user?.name?.charAt(0) ?? '؟'}
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <p className="text-sm font-bold text-slate-900 dark:text-white truncate">
                                                        {enrollment.user?.name}
                                                    </p>
                                                    <p className="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                                        {enrollment.course?.title}
                                                    </p>
                                                </div>
                                                <p className="text-xs text-slate-400 shrink-0 mt-0.5">
                                                    {new Date(enrollment.enrolled_at).toLocaleDateString('ar')}
                                                </p>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="flex flex-col items-center justify-center py-12 text-center px-6">
                                        <div className="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                                            <svg className="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <p className="text-sm text-slate-500 dark:text-slate-400">لا يوجد تسجيلات بعد</p>
                                    </div>
                                )}
                            </div>

                            {/* Quick Links */}
                            <div className="mt-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm p-5">
                                <h3 className="font-bold text-slate-900 dark:text-white text-sm mb-4">روابط سريعة</h3>
                                <div className="space-y-2">
                                    {[
                                        { href: '/instructor/courses/create', label: 'إنشاء دورة جديدة', icon: 'M12 4v16m8-8H4' },
                                        { href: '/instructor/courses', label: 'إدارة الدورات', icon: 'M4 6h16M4 10h16M4 14h16M4 18h16' },
                                        { href: '/instructor/financials', label: 'الأرباح والمالية', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1' },
                                    ].map((link, i) => (
                                        <Link
                                            key={i}
                                            href={link.href}
                                            className="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors group"
                                        >
                                            <div className="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                                                <svg className="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={link.icon} />
                                                </svg>
                                            </div>
                                            <span className="text-sm font-medium text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {link.label}
                                            </span>
                                            <svg className="w-4 h-4 text-slate-300 dark:text-slate-600 mr-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </Link>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
