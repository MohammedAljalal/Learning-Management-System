import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState, useRef } from 'react';
import AppLayout from '@/layouts/AppLayout';
import Button from '@/components/ui/Button';
import type { PageProps, Course, Section } from '@/types';

interface Quiz {
    id: number;
    title: string;
    time_limit_minutes: number;
    is_final_exam: boolean;
    questions_count?: number;
}

interface SectionWithQuizzes extends Section {
    quizzes: Quiz[];
}

interface Props extends PageProps {
    course: Course & { quizzes: Quiz[] };
    sections: SectionWithQuizzes[];
    flash?: { success?: string; error?: string };
}

export default function CourseBuilder({ course, sections, flash }: Props) {
    // Section state
    const [addingSection, setAddingSection] = useState(false);
    const [newSectionTitle, setNewSectionTitle] = useState('');
    
    // Lesson state
    const [addingLessonTo, setAddingLessonTo] = useState<number | null>(null);
    const fileInputRef = useRef<HTMLInputElement>(null);
    
    // Course edit state
    const [editingCourse, setEditingCourse] = useState(false);
    const [coverPreview, setCoverPreview] = useState<string | null>(course.thumbnail_url || null);
    const coverInputRef = useRef<HTMLInputElement>(null);

    const lessonForm = useForm({
        title: '',
        content: '',
        video: null as File | null,
    });

    const courseEditForm = useForm({
        title: course.title,
        description: course.description || '',
        price: String(course.price),
        difficulty: course.difficulty || 'beginner',
        status: (course as any).status || 'draft',
        cover_image: null as File | null,
    });

    // ─── Section actions ───────────────────────────────────
    const addSection = (e: React.FormEvent) => {
        e.preventDefault();
        router.post(`/instructor/courses/${course.id}/sections`, { title: newSectionTitle }, {
            preserveScroll: true,
            onSuccess: () => { setAddingSection(false); setNewSectionTitle(''); }
        });
    };

    // ─── Lesson actions ────────────────────────────────────
    const addLesson = (e: React.FormEvent) => {
        e.preventDefault();
        if (!addingLessonTo) return;
        lessonForm.post(`/instructor/sections/${addingLessonTo}/lessons`, {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => closeLessonModal(),
        });
    };

    const closeLessonModal = () => {
        setAddingLessonTo(null);
        lessonForm.reset();
        lessonForm.clearErrors();
        if (fileInputRef.current) fileInputRef.current.value = '';
    };

    // ─── Quiz actions ──────────────────────────────────────
    const addSectionQuiz = (sectionId: number, sectionTitle: string) => {
        router.post(`/instructor/sections/${sectionId}/quizzes`, {
            title: `اختبار وحدة: ${sectionTitle}`,
        }, { preserveScroll: true });
    };

    const addFinalExam = () => {
        router.post(`/instructor/courses/${course.id}/final-exam`, {
            title: `الاختبار النهائي: ${course.title}`,
        }, { preserveScroll: true });
    };

    // ─── Course edit actions ───────────────────────────────
    const saveCourse = (e: React.FormEvent) => {
        e.preventDefault();
        courseEditForm.post(`/instructor/courses/${course.id}/update`, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => setEditingCourse(false),
        });
    };

    const handleCoverChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            courseEditForm.setData('cover_image', file);
            setCoverPreview(URL.createObjectURL(file));
        }
    };

    const difficultyLabel: Record<string, string> = {
        beginner: 'مبتدئ',
        intermediate: 'متوسط',
        expert: 'متقدم',
    };

    const statusLabel: Record<string, { label: string; color: string }> = {
        draft: { label: 'مسودة', color: 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400' },
        published: { label: 'منشورة', color: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
        archived: { label: 'مؤرشفة', color: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' },
    };
    const currentStatus = (course as any).status || 'draft';

    return (
        <AppLayout>
            <Head title={`منشئ الدورة - ${course.title}`} />
            <div className="py-8 min-h-screen bg-slate-50 dark:bg-slate-900" dir="rtl">
                <div className="max-w-5xl mx-auto sm:px-6 lg:px-8">

                    {/* Flash Messages */}
                    {flash?.success && (
                        <div className="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-700 dark:text-emerald-300 text-sm flex items-center gap-2">
                            <svg className="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                            </svg>
                            {flash.success}
                        </div>
                    )}
                    {flash?.error && (
                        <div className="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-400 text-sm">
                            {flash.error}
                        </div>
                    )}

                    {/* Header */}
                    <div className="flex items-start justify-between mb-8 gap-4">
                        <div className="flex items-center gap-3">
                            <Link href="/instructor/courses" className="text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                            <div>
                                <div className="flex items-center gap-3">
                                    <h1 className="text-2xl font-black text-slate-900 dark:text-white">منشئ الدورة</h1>
                                    <span className={`px-2.5 py-0.5 text-xs font-bold rounded-full ${statusLabel[currentStatus].color}`}>
                                        {statusLabel[currentStatus].label}
                                    </span>
                                </div>
                                <p className="text-slate-500 dark:text-slate-400 text-sm mt-0.5">{course.title}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-2 shrink-0">
                            {/* Publish / Unpublish Button */}
                            {currentStatus === 'published' ? (
                                <button
                                    onClick={() => {
                                        if (confirm('هل تريد إلغاء نشر هذه الدورة وجعلها مسودة؟')) {
                                            router.post(`/instructor/courses/${course.id}/update`, {
                                                title: course.title,
                                                description: course.description || '',
                                                price: String(course.price),
                                                difficulty: (course as any).difficulty || 'beginner',
                                                status: 'draft',
                                            }, { preserveScroll: true });
                                        }
                                    }}
                                    className="flex items-center gap-2 px-4 py-2 text-sm font-bold text-amber-700 bg-amber-100 hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-900/50 border border-amber-200 dark:border-amber-800 rounded-xl transition-all"
                                >
                                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    إلغاء النشر
                                </button>
                            ) : (
                                <button
                                    onClick={() => {
                                        router.post(`/instructor/courses/${course.id}/update`, {
                                            title: course.title,
                                            description: course.description || '',
                                            price: String(course.price),
                                            difficulty: (course as any).difficulty || 'beginner',
                                            status: 'published',
                                        }, { preserveScroll: true });
                                    }}
                                    className="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl transition-all shadow-lg shadow-emerald-600/25"
                                >
                                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                    </svg>
                                    نشر الدورة
                                </button>
                            )}

                            {/* Edit Button */}
                            <button
                                onClick={() => setEditingCourse(!editingCourse)}
                                className="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all"
                            >
                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                {editingCourse ? 'إغلاق التعديل' : 'تعديل الدورة'}
                            </button>
                        </div>
                    </div>

                    {/* Course Edit Panel */}
                    {editingCourse && (
                        <form onSubmit={saveCourse} className="bg-white dark:bg-slate-800 border border-indigo-200 dark:border-indigo-800/50 rounded-2xl p-6 mb-8 shadow-sm">
                            <h2 className="text-lg font-bold text-slate-800 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">تعديل تفاصيل الدورة</h2>

                            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                {/* Left: Cover */}
                                <div>
                                    <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">صورة الغلاف</label>
                                    <div
                                        onClick={() => coverInputRef.current?.click()}
                                        className="relative h-40 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 hover:border-indigo-400 cursor-pointer overflow-hidden bg-slate-50 dark:bg-slate-900 transition-colors"
                                    >
                                        {coverPreview ? (
                                            <>
                                                <img src={coverPreview} alt="" className="w-full h-full object-cover" />
                                                <div className="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                    <span className="text-white text-xs font-semibold">تغيير الصورة</span>
                                                </div>
                                            </>
                                        ) : (
                                            <div className="flex flex-col items-center justify-center h-full">
                                                <svg className="w-8 h-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span className="text-xs text-slate-400">رفع صورة</span>
                                            </div>
                                        )}
                                        <input ref={coverInputRef} type="file" className="sr-only" accept="image/*" onChange={handleCoverChange} />
                                    </div>
                                </div>

                                {/* Right: Fields */}
                                <div className="lg:col-span-2 space-y-4">
                                    <div>
                                        <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">عنوان الدورة *</label>
                                        <input
                                            type="text"
                                            value={courseEditForm.data.title}
                                            onChange={e => courseEditForm.setData('title', e.target.value)}
                                            className="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                            required
                                        />
                                    </div>

                                    <div className="grid grid-cols-3 gap-3">
                                        <div>
                                            <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">السعر ($)</label>
                                            <input
                                                type="number" min="0" step="0.01"
                                                value={courseEditForm.data.price}
                                                onChange={e => courseEditForm.setData('price', e.target.value)}
                                                className="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">المستوى</label>
                                            <select
                                                value={courseEditForm.data.difficulty}
                                                onChange={e => courseEditForm.setData('difficulty', e.target.value as any)}
                                                className="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                            >
                                                <option value="beginner">مبتدئ</option>
                                                <option value="intermediate">متوسط</option>
                                                <option value="expert">متقدم</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">الحالة</label>
                                            <select
                                                value={courseEditForm.data.status}
                                                onChange={e => courseEditForm.setData('status', e.target.value as any)}
                                                className="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                            >
                                                <option value="draft">مسودة</option>
                                                <option value="published">منشورة</option>
                                                <option value="archived">مؤرشفة</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">وصف الدورة *</label>
                                        <textarea
                                            value={courseEditForm.data.description}
                                            onChange={e => courseEditForm.setData('description', e.target.value)}
                                            rows={3}
                                            className="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div className="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                                <button type="button" onClick={() => setEditingCourse(false)} className="px-5 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                                    إلغاء
                                </button>
                                <Button type="submit" loading={courseEditForm.processing}>حفظ التغييرات</Button>
                            </div>
                        </form>
                    )}

                    {/* Sections List */}
                    <div className="space-y-6">
                        {sections.map((section, sIdx) => (
                            <div key={section.id} className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm overflow-hidden">
                                {/* Section Header */}
                                <div className="p-4 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                    <div className="flex items-center gap-3">
                                        <div className="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-xs font-black text-indigo-600 dark:text-indigo-400">
                                            {sIdx + 1}
                                        </div>
                                        <h3 className="font-bold text-slate-900 dark:text-white">{section.title}</h3>
                                        <span className="text-xs text-slate-400">{section.lessons.length} درس</span>
                                    </div>
                                    <button
                                        onClick={() => setAddingLessonTo(section.id)}
                                        className="flex items-center gap-1.5 text-indigo-600 dark:text-indigo-400 text-sm font-semibold hover:bg-indigo-50 dark:hover:bg-indigo-900/20 px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                                        </svg>
                                        إضافة درس
                                    </button>
                                </div>

                                {/* Lessons */}
                                <div className="divide-y divide-slate-100 dark:divide-slate-700">
                                    {section.lessons.map((lesson, lIdx) => (
                                        <div key={lesson.id} className="p-4 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
                                            <div className="flex items-center gap-3">
                                                <div className="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0 text-xs font-bold text-slate-500">
                                                    {lIdx + 1}
                                                </div>
                                                <div>
                                                    <p className="text-sm font-medium text-slate-700 dark:text-slate-300">{lesson.title}</p>
                                                    {lesson.duration_seconds && (
                                                        <p className="text-xs text-slate-400 mt-0.5">
                                                            {Math.floor(lesson.duration_seconds / 60)} دقيقة
                                                        </p>
                                                    )}
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button className="text-xs px-2.5 py-1 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors">تعديل</button>
                                                <button className="text-xs px-2.5 py-1 text-slate-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">حذف</button>
                                            </div>
                                        </div>
                                    ))}

                                    {section.lessons.length === 0 && (
                                        <div className="p-6 text-center text-slate-400 text-sm">
                                            لا يوجد دروس في هذه الوحدة بعد.
                                        </div>
                                    )}
                                </div>

                                {/* Section Quiz */}
                                <div className="p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30">
                                    {section.quizzes && section.quizzes.length > 0 ? (
                                        <div className="flex items-center justify-between">
                                            <div className="flex items-center gap-2.5">
                                                <div className="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center shrink-0">
                                                    <svg className="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <span className="text-sm font-semibold text-slate-700 dark:text-slate-300">{section.quizzes[0].title}</span>
                                                    <p className="text-xs text-slate-400">{section.quizzes[0].time_limit_minutes} دقيقة</p>
                                                </div>
                                            </div>
                                            <Button
                                                size="sm"
                                                variant="secondary"
                                                onClick={() => router.get(`/instructor/quizzes/${section.quizzes[0].id}/manage`)}
                                            >
                                                إدارة الأسئلة
                                            </Button>
                                        </div>
                                    ) : (
                                        <button
                                            onClick={() => addSectionQuiz(section.id, section.title)}
                                            className="w-full py-2 border-2 border-dashed border-indigo-200 dark:border-indigo-800/50 rounded-xl text-indigo-600 dark:text-indigo-400 text-sm font-semibold hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors flex items-center justify-center gap-2"
                                        >
                                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                                            </svg>
                                            إضافة اختبار للوحدة
                                        </button>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Add Section */}
                    {addingSection ? (
                        <form onSubmit={addSection} className="mt-6 bg-white dark:bg-slate-800 border border-indigo-200 dark:border-indigo-800/50 rounded-2xl p-4 shadow-sm">
                            <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">عنوان الوحدة الجديدة</label>
                            <input
                                type="text"
                                autoFocus
                                placeholder="مثال: مقدمة في البرمجة"
                                value={newSectionTitle}
                                onChange={e => setNewSectionTitle(e.target.value)}
                                className="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white mb-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            />
                            <div className="flex justify-end gap-2">
                                <button type="button" onClick={() => { setAddingSection(false); setNewSectionTitle(''); }} className="px-4 py-2 text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors">إلغاء</button>
                                <button type="submit" disabled={!newSectionTitle.trim()} className="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold disabled:opacity-50 transition-colors">إضافة الوحدة</button>
                            </div>
                        </form>
                    ) : (
                        <button
                            onClick={() => setAddingSection(true)}
                            className="mt-6 w-full py-4 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-2xl text-slate-500 dark:text-slate-400 font-semibold hover:border-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors flex items-center justify-center gap-2"
                        >
                            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                            </svg>
                            إضافة وحدة جديدة
                        </button>
                    )}

                    {/* Final Exam */}
                    <div className="mt-10 bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/10 dark:to-indigo-900/10 border border-purple-100 dark:border-purple-800/30 rounded-2xl p-6">
                        <div className="flex items-center gap-4 mb-5">
                            <div className="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center">
                                <svg className="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <div>
                                <h2 className="text-lg font-black text-slate-900 dark:text-white">الاختبار النهائي للدورة</h2>
                                <p className="text-sm text-slate-500 dark:text-slate-400">تقييم شامل لمعرفة الطالب عند إنهاء الدورة</p>
                            </div>
                        </div>

                        {course.quizzes && course.quizzes.length > 0 ? (
                            <div className="flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-purple-200 dark:border-purple-800/30">
                                <div>
                                    <p className="font-semibold text-slate-700 dark:text-slate-300">{course.quizzes[0].title}</p>
                                    <p className="text-sm text-slate-400 mt-0.5">{course.quizzes[0].time_limit_minutes} دقيقة</p>
                                </div>
                                <Button
                                    onClick={() => router.get(`/instructor/quizzes/${course.quizzes![0].id}/manage`)}
                                >
                                    إدارة الأسئلة
                                </Button>
                            </div>
                        ) : (
                            <button
                                onClick={addFinalExam}
                                className="w-full py-3 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2"
                            >
                                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                                </svg>
                                إنشاء الاختبار النهائي
                            </button>
                        )}
                    </div>

                </div>
            </div>

            {/* Add Lesson Modal */}
            {addingLessonTo && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" dir="rtl">
                    <div className="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-200 dark:border-slate-700">
                        <div className="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <h3 className="text-lg font-bold text-slate-900 dark:text-white">إضافة درس جديد</h3>
                            <button onClick={closeLessonModal} className="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form onSubmit={addLesson} className="p-6 space-y-5">
                            <div>
                                <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">عنوان الدرس <span className="text-red-500">*</span></label>
                                <input
                                    type="text"
                                    value={lessonForm.data.title}
                                    onChange={e => lessonForm.setData('title', e.target.value)}
                                    className={`w-full rounded-xl border ${lessonForm.errors.title ? 'border-red-400' : 'border-slate-300 dark:border-slate-600'} bg-white dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500`}
                                    placeholder="مثال: مقدمة عن المتغيرات"
                                    required
                                />
                                {lessonForm.errors.title && <p className="mt-1 text-xs text-red-500">{lessonForm.errors.title}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">وصف الدرس (اختياري)</label>
                                <textarea
                                    value={lessonForm.data.content}
                                    onChange={e => lessonForm.setData('content', e.target.value)}
                                    rows={2}
                                    className="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="وصف مختصر لما يتعلمه الطالب..."
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                    فيديو الدرس <span className="text-red-500">*</span>
                                    <span className="text-xs font-normal text-slate-400 mr-1">(MP4, MOV - حتى 500MB)</span>
                                </label>
                                <input
                                    type="file"
                                    ref={fileInputRef}
                                    accept="video/mp4,video/quicktime,video/x-msvideo,video/webm"
                                    onChange={e => lessonForm.setData('video', e.target.files?.[0] || null)}
                                    className="block w-full text-sm text-slate-500 dark:text-slate-400 file:ml-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400 cursor-pointer"
                                    required
                                />
                                {lessonForm.errors.video && <p className="mt-1 text-xs text-red-500">{lessonForm.errors.video}</p>}
                            </div>

                            {/* Upload Progress */}
                            {lessonForm.progress && (
                                <div>
                                    <div className="flex justify-between text-xs text-slate-500 mb-1">
                                        <span>جاري رفع الفيديو...</span>
                                        <span className="font-bold text-indigo-600">{lessonForm.progress.percentage}%</span>
                                    </div>
                                    <div className="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                        <div
                                            className="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                                            style={{ width: `${lessonForm.progress.percentage}%` }}
                                        />
                                    </div>
                                </div>
                            )}

                            <div className="flex justify-end gap-3 pt-2 border-t border-slate-100 dark:border-slate-700">
                                <Button type="button" variant="secondary" onClick={closeLessonModal} disabled={lessonForm.processing}>إلغاء</Button>
                                <Button type="submit" loading={lessonForm.processing}>
                                    {lessonForm.processing ? 'جاري الرفع...' : 'حفظ الدرس'}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
