import { Head, router } from '@inertiajs/react';
import { useState, useCallback } from 'react';
import AppLayout from '@/layouts/AppLayout';
import CourseCard from '@/components/CourseCard';
import type { PageProps, Course, Category, PaginatedData } from '@/types';

interface Props extends PageProps {
    courses: PaginatedData<Course>;
    categories: Category[];
    filters: { search?: string; category?: string; difficulty?: string };
}

export default function CourseCatalog({ courses, categories, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [category, setCategory] = useState(filters.category ?? '');
    const [difficulty, setDifficulty] = useState(filters.difficulty ?? '');

    const applyFilters = useCallback((params: Record<string, string>) => {
        router.get('/courses', { search, category, difficulty, ...params }, {
            preserveState: true,
            replace: true,
        });
    }, [search, category, difficulty]);

    const handleSearch = (val: string) => {
        setSearch(val);
        const id = setTimeout(() => applyFilters({ search: val }), 350);
        return () => clearTimeout(id);
    };

    return (
        <AppLayout>
            <Head title="الدورات التدريبية" />
            <div className="py-12 min-h-screen" dir="rtl">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">

                    {/* Header */}
                    <div className="mb-8">
                        <h1 className="text-2xl font-black text-slate-900 dark:text-white">الدورات التدريبية</h1>
                        <p className="text-slate-500 dark:text-slate-400 text-sm mt-1">{courses.total} دورة متاحة</p>
                    </div>

                    {/* Filters Bar */}
                    <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm p-4 mb-8 flex flex-col md:flex-row gap-4 items-center">
                        {/* Search */}
                        <div className="relative flex-1 w-full">
                            <svg className="absolute end-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                type="search"
                                value={search}
                                onChange={e => handleSearch(e.target.value)}
                                placeholder="ابحث عن دورة أو مدرب..."
                                className="w-full ps-4 pe-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-sm"
                            />
                        </div>

                        {/* Category */}
                        <select
                            value={category}
                            onChange={e => { setCategory(e.target.value); applyFilters({ category: e.target.value }); }}
                            className="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2.5 pl-10 pr-4 text-sm focus:ring-2 focus:ring-indigo-500 transition appearance-none"
                            style={{ backgroundPosition: 'left 0.75rem center' }}
                        >
                            <option value="">كل التصنيفات</option>
                            {categories.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                        </select>

                        {/* Difficulty */}
                        <select
                            value={difficulty}
                            onChange={e => { setDifficulty(e.target.value); applyFilters({ difficulty: e.target.value }); }}
                            className="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 py-2.5 pl-10 pr-4 text-sm focus:ring-2 focus:ring-indigo-500 transition appearance-none"
                            style={{ backgroundPosition: 'left 0.75rem center' }}
                        >
                            <option value="">كل المستويات</option>
                            <option value="beginner">مبتدئ</option>
                            <option value="intermediate">متوسط</option>
                            <option value="expert">خبير</option>
                        </select>
                    </div>

                    {/* Grid */}
                    {courses.data.length > 0 ? (
                        <>
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                {courses.data.map(course => (
                                    <CourseCard key={course.id} course={course} />
                                ))}
                            </div>

                            {/* Pagination */}
                            {courses.last_page > 1 && (
                                <div className="mt-10 flex justify-center gap-2">
                                    {courses.links.map((link, i) => (
                                        <button
                                            key={i}
                                            disabled={!link.url}
                                            onClick={() => link.url && router.visit(link.url)}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                            className={`px-4 py-2 rounded-xl text-sm font-semibold transition-all ${
                                                link.active
                                                    ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25'
                                                    : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed'
                                            }`}
                                        />
                                    ))}
                                </div>
                            )}
                        </>
                    ) : (
                        <div className="flex flex-col items-center justify-center py-24 text-center">
                            <div className="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                                <svg className="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                            <h3 className="text-lg font-bold text-slate-700 dark:text-slate-300 mb-1">لا توجد دورات</h3>
                            <p className="text-sm text-slate-500 dark:text-slate-400">لم يتم العثور على دورات تطابق بحثك. جرّب كلمات أخرى.</p>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
