import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import type { PageProps, Course } from '@/types';

interface Props extends PageProps {
    course: Course;
    isEnrolled: boolean;
}

export default function CourseShow({ course, isEnrolled, auth }: Props) {

    return (
        <AppLayout>
            <Head title={course.title} />
            <div className="py-12 min-h-screen" dir="rtl">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-8">
                        <div>
                            <div className="flex items-center gap-3 mb-4">
                                <span className="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 text-xs font-bold px-3 py-1 rounded-full">
                                    {course.category?.name}
                                </span>
                            </div>
                            <h1 className="text-3xl font-black text-slate-900 dark:text-white mb-4 leading-tight">{course.title}</h1>
                            <p className="text-slate-600 dark:text-slate-400 leading-relaxed text-lg mb-6">{course.description}</p>
                            
                            <div className="flex items-center gap-4 py-4 border-t border-b border-slate-200 dark:border-slate-800">
                                <div className="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shrink-0 overflow-hidden shadow-md">
                                    {(course.instructor as any)?.avatar_url ? (
                                        <img
                                            src={(course.instructor as any).avatar_url}
                                            alt={course.instructor?.name}
                                            className="w-full h-full object-cover"
                                        />
                                    ) : (
                                        <span className="font-bold text-white text-lg">
                                            {course.instructor?.name?.charAt(0)}
                                        </span>
                                    )}
                                </div>
                                <div>
                                    <p className="text-sm font-bold text-slate-900 dark:text-white">{course.instructor?.name}</p>
                                    <p className="text-xs text-slate-500">المدرب</p>
                                    {(course.instructor as any)?.bio && (
                                        <p className="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md">
                                            {(course.instructor as any).bio}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Curriculum */}
                        <div>
                            <h2 className="text-xl font-bold text-slate-900 dark:text-white mb-6">محتوى الدورة</h2>
                            <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm overflow-hidden">
                                {course.sections?.map((section, idx) => (
                                    <div key={section.id} className={`${idx !== 0 ? 'border-t border-slate-200 dark:border-slate-700' : ''}`}>
                                        <div className="bg-slate-50 dark:bg-slate-800/80 px-6 py-4">
                                            <h3 className="font-bold text-slate-900 dark:text-white text-sm">{section.title}</h3>
                                        </div>
                                        <div className="divide-y divide-slate-100 dark:divide-slate-700/50">
                                            {section.lessons?.map(lesson => (
                                                <div key={lesson.id} className="px-6 py-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                                    <div className="flex items-center gap-3">
                                                        <svg className="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span className="text-sm font-medium text-slate-700 dark:text-slate-300">{lesson.title}</span>
                                                    </div>
                                                </div>
                                            ))}
                                            {(!section.lessons || section.lessons.length === 0) && (
                                                <div className="px-6 py-4 text-xs text-slate-500">لا يوجد دروس في هذا القسم بعد.</div>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Sidebar */}
                    <div>
                        <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-3xl p-2 shadow-sm sticky top-24">
                            <div className="w-full aspect-video rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 mb-4 overflow-hidden shadow-inner">
                                {course.thumbnail_url && (
                                    <img src={course.thumbnail_url} alt={course.title} className="w-full h-full object-cover" />
                                )}
                            </div>
                            <div className="p-4">
                                <div className="text-3xl font-black text-slate-900 dark:text-white mb-6">
                                    {course.price > 0 ? `$${Number(course.price).toFixed(2)}` : 'مجاني'}
                                </div>
                                {(() => {
                                    const isAdmin = auth.user?.roles?.includes('Super Admin');
                                    const isInstructor = auth.user?.roles?.includes('Instructor');
                                    const isCourseOwner = auth.user?.id === course.instructor_id;

                                    // Admin or instructor: show browse button, never enroll button
                                    if (isAdmin || isInstructor) {
                                        return (
                                            <Link
                                                href={`/courses/${course.slug}/learn/${course.sections?.[0]?.lessons?.[0]?.id ?? ''}`}
                                                className="flex items-center justify-center w-full py-4 bg-slate-700 hover:bg-slate-600 text-white font-bold rounded-xl transition-all"
                                            >
                                                {isCourseOwner ? 'عرض الدورة (أنت المدرب)' : 'تصفح محتوى الدورة'}
                                            </Link>
                                        );
                                    }

                                    // Student enrolled
                                    if (isEnrolled) {
                                        return (
                                            <Link
                                                href={`/courses/${course.slug}/learn/${course.sections?.[0]?.lessons?.[0]?.id ?? ''}`}
                                                className="flex items-center justify-center w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-600/30"
                                            >
                                                متابعة التعلم
                                            </Link>
                                        );
                                    }

                                    // Student not enrolled
                                    return course.price > 0 ? (
                                        <Link
                                            href={`/checkout/${course.slug}`}
                                            className="flex items-center justify-center w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-indigo-600/30"
                                        >
                                            سجل الآن للوصول الكامل
                                        </Link>
                                    ) : (
                                        <Link
                                            href={`/courses/${course.slug}/enroll`}
                                            method="post"
                                            as="button"
                                            className="flex items-center justify-center w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-indigo-600/30"
                                        >
                                            سجل الآن مجاناً
                                        </Link>
                                    );
                                })()}
                                <div className="mt-6 space-y-3">
                                    <div className="flex justify-between text-sm">
                                        <span className="text-slate-500">المستوى</span>
                                        <span className="font-semibold text-slate-900 dark:text-white">{course.difficulty}</span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-slate-500">تاريخ النشر</span>
                                        <span className="font-semibold text-slate-900 dark:text-white">{new Date(course.created_at).toLocaleDateString('ar')}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </AppLayout>
    );
}
