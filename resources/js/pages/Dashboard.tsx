import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import type { PageProps, Enrollment, Certificate, XpTransaction, Notification } from '@/types';

interface Props extends PageProps {
    enrollments: Enrollment[];
    progress: Record<number, number>;
    certificates: Certificate[];
    recentXp: XpTransaction[];
    recentNotifications: Notification[];
    totalXp: number;
    level: number;
    levelProgress: number;
    xpForNextLevel: number;
}

export default function Dashboard({ enrollments, progress, certificates, recentXp, recentNotifications, totalXp, level, levelProgress, xpForNextLevel }: Props) {
    return (
        <AppLayout>
            <Head title="لوحة التحكم" />
            <div className="py-8 min-h-screen" dir="rtl">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                    {/* XP Banner */}
                    <div className="bg-gradient-to-l from-indigo-600 to-purple-700 rounded-2xl p-6 text-white shadow-xl shadow-indigo-600/20 flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div className="flex items-center gap-5">
                            <div className="relative">
                                <div className="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-black">
                                    {level}
                                </div>
                                <div className="absolute -bottom-1 -end-1 bg-amber-400 text-slate-900 text-xs font-black px-1.5 py-0.5 rounded-full">
                                    Lv
                                </div>
                            </div>
                            <div>
                                <p className="text-white/70 text-sm">المستوى الحالي</p>
                                <h3 className="text-2xl font-black">المستوى {level}</h3>
                                <p className="text-white/60 text-xs mt-0.5">{totalXp.toLocaleString('ar')} XP إجمالي</p>
                            </div>
                        </div>
                        <div className="w-full sm:w-72">
                            <div className="flex justify-between text-xs text-white/70 mb-1.5">
                                <span>تقدم للمستوى {level + 1}</span>
                                <span>{xpForNextLevel} XP متبقية</span>
                            </div>
                            <div className="w-full bg-white/20 rounded-full h-3 overflow-hidden">
                                <div className="bg-amber-400 h-3 rounded-full transition-all duration-700" style={{ width: `${levelProgress}%` }} />
                            </div>
                            <p className="text-xs text-white/50 mt-1 text-center">{levelProgress}٪</p>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {/* Left: Courses + Certificates */}
                        <div className="lg:col-span-2 space-y-6">

                            {/* Enrolled Courses */}
                            <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm p-6">
                                <div className="flex items-center justify-between mb-5">
                                    <h3 className="font-bold text-slate-900 dark:text-white">دوراتي المسجّلة</h3>
                                    <Link href="/courses" className="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">استعرض المزيد</Link>
                                </div>
                                {enrollments.length > 0 ? (
                                    <div className="space-y-4">
                                        {enrollments.map(enrollment => {
                                            const course = enrollment.course;
                                            const pct = progress[course.id] ?? 0;
                                            return (
                                                <div key={enrollment.id} className="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors group">
                                                    <div className="w-16 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shrink-0 overflow-hidden">
                                                        {course.thumbnail_url && (
                                                            <img src={course.thumbnail_url} alt={course.title} className="w-full h-full object-cover" />
                                                        )}
                                                    </div>
                                                    <div className="flex-1 min-w-0">
                                                        <h4 className="text-sm font-bold text-slate-900 dark:text-white truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                            {course.title}
                                                        </h4>
                                                        <p className="text-xs text-slate-500 dark:text-slate-400">{course.instructor?.name}</p>
                                                        <div className="flex items-center gap-2 mt-1.5">
                                                            <div className="flex-1 bg-slate-200 dark:bg-slate-700 rounded-full h-1.5">
                                                                <div className="bg-indigo-500 h-1.5 rounded-full transition-all" style={{ width: `${pct}%` }} />
                                                            </div>
                                                            <span className="text-xs text-slate-500 dark:text-slate-400 shrink-0">{pct}٪</span>
                                                        </div>
                                                    </div>
                                                    <Link href={`/courses/${course.slug}`}
                                                        className="shrink-0 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors">
                                                        {pct > 0 ? 'متابعة' : 'ابدأ'}
                                                    </Link>
                                                </div>
                                            );
                                        })}
                                    </div>
                                ) : (
                                    <div className="text-center py-12">
                                        <div className="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <svg className="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <p className="text-sm text-slate-500 dark:text-slate-400">لم تسجّل في أي دورة بعد.</p>
                                        <Link href="/courses" className="mt-3 inline-block text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">استعرض الدورات</Link>
                                    </div>
                                )}
                            </div>

                            {/* Certificates */}
                            {certificates.length > 0 && (
                                <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm p-6">
                                    <h3 className="font-bold text-slate-900 dark:text-white mb-5">شهاداتي</h3>
                                    <div className="space-y-3">
                                        {certificates.map(cert => (
                                            <div key={cert.id} className="flex items-center justify-between p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-9 h-9 rounded-xl bg-amber-500 flex items-center justify-center">
                                                        <svg className="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p className="text-sm font-bold text-slate-900 dark:text-white">{cert.course?.title}</p>
                                                        <p className="text-xs text-slate-500">{new Date(cert.issued_at).toLocaleDateString('ar')}</p>
                                                    </div>
                                                </div>
                                                <div className="flex gap-3">
                                                    <a href={cert.download_url} className="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">تحميل</a>
                                                    <a href={cert.verify_url} target="_blank" rel="noreferrer" className="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">تحقق</a>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Right: XP + Notifications */}
                        <div className="space-y-6">
                            {/* Recent XP */}
                            <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm p-6">
                                <h3 className="font-bold text-slate-900 dark:text-white mb-4">آخر نقاط XP المكتسبة</h3>
                                {recentXp.length > 0 ? (
                                    <div className="space-y-3">
                                        {recentXp.map(tx => (
                                            <div key={tx.id} className="flex items-center justify-between">
                                                <p className="text-sm text-slate-700 dark:text-slate-300 leading-snug">{tx.description}</p>
                                                <span className="text-sm font-black text-emerald-600 dark:text-emerald-400 shrink-0 ms-2">+{tx.amount}</span>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-sm text-slate-500 dark:text-slate-400 text-center py-4">لم تكتسب نقاط بعد.</p>
                                )}
                            </div>

                            {/* Notifications */}
                            <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm p-6">
                                <h3 className="font-bold text-slate-900 dark:text-white mb-4">الإشعارات الأخيرة</h3>
                                {recentNotifications.length > 0 ? (
                                    <div className="space-y-3">
                                        {recentNotifications.map(notif => (
                                            <div key={notif.id} className={`flex items-start gap-3 ${notif.read_at ? 'opacity-60' : 'opacity-100'}`}>
                                                <div className={`w-2 h-2 rounded-full mt-2 shrink-0 ${notif.read_at ? 'bg-slate-300 dark:bg-slate-600' : 'bg-indigo-500'}`} />
                                                <div>
                                                    <p className="text-sm text-slate-700 dark:text-slate-300">{notif.data?.message ?? 'إشعار جديد'}</p>
                                                    <p className="text-xs text-slate-400 mt-0.5">{notif.created_at}</p>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-sm text-slate-500 dark:text-slate-400 text-center py-4">لا توجد إشعارات.</p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
