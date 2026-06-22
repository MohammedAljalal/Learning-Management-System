import { Link } from '@inertiajs/react';
import type { Course } from '@/types';

interface Props {
    course: Course;
}

const difficultyMap = {
    beginner: { label: 'مبتدئ', color: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' },
    intermediate: { label: 'متوسط', color: 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' },
    expert: { label: 'خبير', color: 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400' },
};

export default function CourseCard({ course }: Props) {
    const diff = difficultyMap[course.difficulty] ?? difficultyMap.beginner;

    return (
        <Link
            href={`/courses/${course.slug}`}
            className="group bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 overflow-hidden flex flex-col"
        >
            {/* Thumbnail */}
            <div className="aspect-video bg-gradient-to-br from-indigo-500 to-purple-600 relative overflow-hidden">
                {course.thumbnail_url ? (
                    <img
                        src={course.thumbnail_url}
                        alt={course.title}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                ) : (
                    <div className="w-full h-full flex items-center justify-center">
                        <svg className="w-12 h-12 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                )}
                {/* Price badge */}
                <div className="absolute top-3 start-3">
                    {course.price > 0 ? (
                        <span className="text-xs font-bold bg-white/95 text-slate-900 px-2.5 py-1 rounded-lg shadow-sm">
                            ${Number(course.price).toFixed(2)}
                        </span>
                    ) : (
                        <span className="text-xs font-bold bg-emerald-500 text-white px-2.5 py-1 rounded-lg shadow-sm">
                            مجاني
                        </span>
                    )}
                </div>
            </div>

            {/* Body */}
            <div className="p-5 flex flex-col flex-1">
                <div className="flex items-center gap-2 mb-3">
                    <span className={`text-xs font-semibold px-2.5 py-1 rounded-lg ${diff.color}`}>
                        {diff.label}
                    </span>
                    {course.category && (
                        <span className="text-xs font-medium px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400">
                            {course.category.name}
                        </span>
                    )}
                </div>

                <h3 className="font-bold text-slate-900 dark:text-white text-sm leading-snug mb-2 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {course.title}
                </h3>
                <p className="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 flex-1 mb-4">
                    {course.description}
                </p>

                {/* Footer */}
                <div className="flex items-center gap-2 pt-3 border-t border-slate-100 dark:border-slate-700 mt-auto">
                    <div className="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center shrink-0 overflow-hidden">
                        {(course.instructor as any)?.avatar_url ? (
                            <img
                                src={(course.instructor as any).avatar_url}
                                alt={course.instructor?.name}
                                className="w-full h-full object-cover"
                            />
                        ) : (
                            <span className="text-xs font-bold text-indigo-600 dark:text-indigo-300">
                                {course.instructor?.name?.charAt(0) ?? 'م'}
                            </span>
                        )}
                    </div>
                    <span className="text-xs text-slate-600 dark:text-slate-400 truncate flex-1">
                        {course.instructor?.name}
                    </span>
                    <span className="text-xs text-slate-400 shrink-0">
                        {course.enrollments_count?.toLocaleString('ar')} طالب
                    </span>
                </div>
            </div>
        </Link>
    );
}
