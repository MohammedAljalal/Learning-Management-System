import { Head, router, useForm, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import AppLayout from '@/layouts/AppLayout';
import Input from '@/components/ui/Input';
import Button from '@/components/ui/Button';
import type { PageProps, Course, Section, Quiz } from '@/types';

interface QuestionOption {
    id: number;
    text: string;
    is_correct: boolean;
}

interface Question {
    id: number;
    text: string;
    points: number;
    options: QuestionOption[];
}

interface QuizWithDetails extends Quiz {
    course: Course;
    section?: Section;
    questions: Question[];
}

interface Props extends PageProps {
    quiz: QuizWithDetails;
}

export default function QuizManager({ quiz, flash }: Props) {
    const [addingQuestion, setAddingQuestion] = useState(false);
    const [editingTimer, setEditingTimer] = useState(false);
    const [timerValue, setTimerValue] = useState(quiz.time_limit_minutes);
    
    useEffect(() => {
        setTimerValue(quiz.time_limit_minutes);
    }, [quiz.time_limit_minutes]);
    
    const saveTimer = (e?: React.FormEvent) => {
        if (e) e.preventDefault();
        router.post(`/instructor/quizzes/${quiz.id}/update-timer`, {
            time_limit_minutes: timerValue,
        }, {
            preserveScroll: true,
            onSuccess: () => {
                setEditingTimer(false);
            },
            onError: (errors) => {
                console.error('فشل الحفظ:', errors);
            }
        });
    };
    
    const questionForm = useForm({
        text: '',
        options: [
            { text: '' },
            { text: '' },
            { text: '' },
            { text: '' }
        ],
        correct_option_index: 0,
    });

    const addQuestion = (e: React.FormEvent) => {
        e.preventDefault();
        questionForm.post(`/instructor/quizzes/${quiz.id}/questions`, {
            preserveScroll: true,
            onSuccess: () => {
                setAddingQuestion(false);
                questionForm.reset();
            }
        });
    };

    const deleteQuestion = (questionId: number) => {
        if (confirm('هل أنت متأكد من حذف هذا السؤال؟')) {
            router.delete(`/instructor/questions/${questionId}`, { preserveScroll: true });
        }
    };

    const handleOptionTextChange = (index: number, text: string) => {
        const newOptions = [...questionForm.data.options];
        newOptions[index].text = text;
        questionForm.setData('options', newOptions);
    };

    return (
        <AppLayout>
            <Head title={`إدارة الاختبار - ${quiz.title}`} />
            
            <div className="py-8 min-h-screen bg-slate-50 dark:bg-slate-900" dir="rtl">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    
                    {/* Header */}
                    <div className="mb-6 flex items-start justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <Link href={`/instructor/courses/${quiz.course.id}/builder`} className="text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                            <div>
                                <div className="flex items-center gap-2">
                                    <h1 className="text-2xl font-black text-slate-900 dark:text-white">إدارة أسئلة الاختبار</h1>
                                    {quiz.is_final_exam ? (
                                        <span className="px-2 py-0.5 text-xs font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 rounded-lg">اختبار نهائي</span>
                                    ) : (
                                        <span className="px-2 py-0.5 text-xs font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 rounded-lg">اختبار وحدة</span>
                                    )}
                                </div>
                                <p className="text-slate-500 dark:text-slate-400 text-sm mt-0.5">{quiz.title}</p>
                            </div>
                        </div>
                        <div className="shrink-0 text-left">
                            <p className="text-xs text-slate-400 mb-1">{quiz.questions.length} سؤال</p>
                        </div>
                    </div>

                    {/* Timer Settings Card */}
                    <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 mb-6 flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                <svg className="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p className="font-semibold text-slate-800 dark:text-white text-sm">مدة الاختبار</p>
                                {editingTimer ? (
                                    <form onSubmit={saveTimer} className="flex items-center gap-2 mt-1">
                                        <input
                                            type="number"
                                            min="5"
                                            max="180"
                                            value={timerValue}
                                            onChange={e => setTimerValue(Number(e.target.value))}
                                            className="w-20 px-2 py-1 text-sm border border-indigo-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
                                            required
                                        />
                                        <span className="text-sm text-slate-500">دقيقة</span>
                                        <button type="submit" className="px-3 py-1 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-500">حفظ</button>
                                        <button type="button" onClick={() => { setEditingTimer(false); setTimerValue(quiz.time_limit_minutes); }} className="px-3 py-1 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 text-xs font-bold rounded-lg hover:bg-slate-300">إلغاء</button>
                                    </form>
                                ) : (
                                    <p className="text-2xl font-black text-amber-600 dark:text-amber-400">{quiz.time_limit_minutes} <span className="text-sm font-normal text-slate-400">دقيقة</span></p>
                                )}
                            </div>
                        </div>
                        {!editingTimer && (
                            <button onClick={() => setEditingTimer(true)} className="text-sm font-semibold text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center gap-1.5 transition-colors">
                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                تعديل المدة
                            </button>
                        )}
                    </div>

                    {/* Questions List */}
                    <div className="space-y-6 mb-8">
                        {quiz.questions.map((question, qIndex) => (
                            <div key={question.id} className="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 relative group">
                                <button 
                                    onClick={() => deleteQuestion(question.id)}
                                    className="absolute top-4 left-4 p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors opacity-0 group-hover:opacity-100"
                                    title="حذف السؤال"
                                >
                                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                
                                <div className="flex gap-4">
                                    <div className="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 shrink-0">
                                        {qIndex + 1}
                                    </div>
                                    <div className="flex-1">
                                        <h3 className="text-lg font-semibold text-slate-800 dark:text-white mb-4">
                                            {question.text}
                                        </h3>
                                        <div className="space-y-2">
                                            {question.options.map((option) => (
                                                <div 
                                                    key={option.id} 
                                                    className={`p-3 rounded-xl border ${option.is_correct ? 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300' : 'bg-slate-50 border-slate-200 text-slate-600 dark:bg-slate-900/50 dark:border-slate-700 dark:text-slate-400'}`}
                                                >
                                                    <div className="flex items-center gap-3">
                                                        <div className={`w-4 h-4 rounded-full border-2 flex items-center justify-center ${option.is_correct ? 'border-green-500' : 'border-slate-300 dark:border-slate-600'}`}>
                                                            {option.is_correct && <div className="w-2 h-2 rounded-full bg-green-500"></div>}
                                                        </div>
                                                        {option.text}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}

                        {quiz.questions.length === 0 && !addingQuestion && (
                            <div className="text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 border-dashed">
                                <svg className="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 className="mt-2 text-sm font-semibold text-slate-900 dark:text-white">لا يوجد أسئلة</h3>
                                <p className="mt-1 text-sm text-slate-500 dark:text-slate-400">ابدأ بإضافة الأسئلة لاختبارك</p>
                            </div>
                        )}
                    </div>

                    {/* Add Question Form */}
                    {addingQuestion ? (
                        <form onSubmit={addQuestion} className="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-indigo-200 dark:border-indigo-800 p-6 md:p-8">
                            <h3 className="text-lg font-bold text-slate-800 dark:text-white mb-6">إضافة سؤال جديد</h3>
                            
                            <div className="space-y-6">
                                <div>
                                    <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">نص السؤال *</label>
                                    <textarea
                                        value={questionForm.data.text}
                                        onChange={e => questionForm.setData('text', e.target.value)}
                                        rows={3}
                                        className="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="اكتب سؤالك هنا..."
                                        required
                                    ></textarea>
                                </div>

                                <div className="space-y-4">
                                    <label className="block text-sm font-medium text-slate-700 dark:text-slate-300">الخيارات (اختر الإجابة الصحيحة) *</label>
                                    {questionForm.data.options.map((option, index) => (
                                        <div key={index} className={`flex items-center gap-3 p-3 rounded-xl border ${questionForm.data.correct_option_index === index ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/20' : 'border-slate-200 dark:border-slate-700'}`}>
                                            <input
                                                type="radio"
                                                name="correct_option"
                                                checked={questionForm.data.correct_option_index === index}
                                                onChange={() => questionForm.setData('correct_option_index', index)}
                                                className="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-slate-300 cursor-pointer"
                                            />
                                            <input
                                                type="text"
                                                value={option.text}
                                                onChange={e => handleOptionTextChange(index, e.target.value)}
                                                className="flex-1 bg-transparent border-none focus:ring-0 text-slate-800 dark:text-white p-0"
                                                placeholder={`الخيار ${index + 1}`}
                                                required
                                            />
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <div className="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-100 dark:border-slate-700">
                                <Button type="button" variant="secondary" onClick={() => setAddingQuestion(false)}>إلغاء</Button>
                                <Button type="submit" loading={questionForm.processing}>حفظ السؤال</Button>
                            </div>
                        </form>
                    ) : (
                        <button
                            onClick={() => setAddingQuestion(true)}
                            className="w-full py-4 border-2 border-dashed border-indigo-300 dark:border-indigo-700 rounded-2xl text-indigo-600 dark:text-indigo-400 font-semibold hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors flex items-center justify-center gap-2"
                        >
                            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                            </svg>
                            إضافة سؤال جديد
                        </button>
                    )}

                </div>
            </div>
        </AppLayout>
    );
}
