import { Head, router, Link } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import AppLayout from '@/layouts/AppLayout';
import type { PageProps, Course } from '@/types';

interface Option {
    id: number;
    text: string;
}

interface Question {
    id: number;
    text: string;
    points: number;
    options: Option[];
}

interface Quiz {
    id: number;
    title: string;
    questions: Question[];
    time_limit_minutes: number;
    is_practice: boolean;
}

interface Attempt {
    id: number;
    started_at: string;
    completed_at: string | null;
    score: number;
    is_passed: boolean;
}

interface Props extends PageProps {
    quiz: Quiz;
    course: Course;
    attempt: Attempt | null;
    nextUrl: string | null;
    certificate: {
        uuid: string;
        issued_at: string;
    } | null;
}

export default function QuizTaking({ quiz, course, attempt, nextUrl, certificate }: Props) {
    const [current, setCurrent] = useState(0);
    const [answers, setAnswers] = useState<Record<number, number>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [timeLeft, setTimeLeft] = useState<number | null>(null);

    const quizRef = useRef<HTMLDivElement>(null);
    const isCompleted = attempt?.completed_at !== null && attempt !== null;
    const isStarted = attempt !== null && attempt?.completed_at === null;

    // Reset local state when a new attempt starts
    useEffect(() => {
        if (isStarted) {
            setCurrent(0);
            setAnswers({});
            setIsSubmitting(false);
        }
    }, [attempt?.id]);

    useEffect(() => {
        // Prevent copying and context menu
        const preventCopy = (e: Event) => e.preventDefault();
        
        if (quizRef.current && isStarted) {
            quizRef.current.addEventListener('copy', preventCopy);
            quizRef.current.addEventListener('contextmenu', preventCopy);
        }

        return () => {
            if (quizRef.current) {
                quizRef.current.removeEventListener('copy', preventCopy);
                quizRef.current.removeEventListener('contextmenu', preventCopy);
            }
        };
    }, [isStarted]);

    // Handle timer
    useEffect(() => {
        if (!isStarted || !quiz.time_limit_minutes) return;

        const start = new Date(attempt!.started_at).getTime();
        const limit = quiz.time_limit_minutes * 60 * 1000;
        const end = start + limit;

        const updateTimer = () => {
            const now = new Date().getTime();
            const remaining = Math.max(0, Math.floor((end - now) / 1000));
            setTimeLeft(remaining);

            if (remaining === 0) {
                // Time is up, auto submit
                submit();
            }
        };

        updateTimer();
        const interval = setInterval(updateTimer, 1000);

        return () => clearInterval(interval);
    }, [isStarted, attempt, quiz.time_limit_minutes]);

    // Handle visibility change (leaving tab)
    useEffect(() => {
        if (!isStarted) return;

        const handleVisibilityChange = () => {
            if (document.hidden && !isSubmitting && !isCompleted) {
                alert('لقد غادرت نافذة الاختبار. تم إنهاء الاختبار واعتباره فاشلاً كإجراء حماية.');
                submit();
            }
        };

        document.addEventListener('visibilitychange', handleVisibilityChange);
        return () => document.removeEventListener('visibilitychange', handleVisibilityChange);
    }, [isStarted, isSubmitting, isCompleted]);

    const formatTime = (seconds: number) => {
        const m = Math.floor(seconds / 60).toString().padStart(2, '0');
        const s = (seconds % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    };

    const select = (optionId: number) => {
        setAnswers(prev => ({ ...prev, [quiz.questions[current].id]: optionId }));
    };

    const startQuiz = () => {
        router.post(`/quizzes/${quiz.id}/start`);
    };

    const submit = () => {
        setIsSubmitting(true);
        router.post(`/quizzes/${quiz.id}/submit`, { answers }, {
            onFinish: () => setIsSubmitting(false),
        });
    };

    if (!quiz.questions || quiz.questions.length === 0) {
        return (
            <AppLayout>
                <div className="py-12 text-center" dir="rtl">
                    <p className="text-slate-500">لا توجد أسئلة في هذا الاختبار.</p>
                </div>
            </AppLayout>
        );
    }

    if (isCompleted) {
        const passed = attempt.is_passed;
        const totalPoints = quiz.questions.reduce((sum, q) => sum + q.points, 0);
        const percentage = Math.round((attempt.score / totalPoints) * 100) || 0;

        return (
            <AppLayout>
                <Head title={`نتيجة: ${quiz.title}`} />
                <div className="min-h-[80vh] py-12" dir="rtl">
                    <div className="max-w-3xl mx-auto px-4">
                        
                        {/* Score Card */}
                        <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-8 shadow-sm text-center mb-10">
                            <div className={`w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 ${passed ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-red-100 dark:bg-red-900/30'}`}>
                                <svg className={`w-12 h-12 ${passed ? 'text-emerald-500' : 'text-red-500'}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    {passed
                                        ? <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        : <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                    }
                                </svg>
                            </div>
                            <h2 className={`text-3xl font-black mb-2 ${passed ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'}`}>
                                {passed ? 'أحسنت! اجتزت الاختبار' : 'حاول مجدداً'}
                            </h2>
                            <p className="text-slate-600 dark:text-slate-400 mb-2">النتيجة</p>
                            <p className="text-6xl font-black text-slate-900 dark:text-white mb-8">{percentage}٪</p>
                            
                            <div className="flex flex-col gap-3 max-w-md mx-auto mb-6">
                                {passed && certificate && (
                                    <a href={`/certificates/${certificate.uuid}/download`} target="_blank" className="w-full px-5 py-4 rounded-xl bg-amber-500 text-white font-bold hover:bg-amber-400 transition-colors flex items-center justify-center gap-3 shadow-lg shadow-amber-500/30">
                                        <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        تحميل الشهادة
                                    </a>
                                )}
                            </div>

                            <div className="flex flex-col sm:flex-row gap-3 justify-center max-w-md mx-auto">
                                {passed && nextUrl ? (
                                    <Link href={nextUrl} className="flex-1 flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-500 transition-colors">
                                        متابعة الدورة
                                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" /></svg>
                                    </Link>
                                ) : null}
                                
                                {!passed && (
                                    <button onClick={startQuiz} className="flex-1 px-5 py-3 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-500 transition-colors">
                                        إعادة المحاولة
                                    </button>
                                )}
                                
                                <Link href={`/courses/${course.slug}`} className="flex-1 px-5 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                    العودة لصفحة الدورة
                                </Link>
                            </div>
                        </div>

                        {/* Detailed Review */}
                        <div className="space-y-8">
                            <h3 className="text-xl font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-200 dark:border-slate-700 pb-4">مراجعة الإجابات</h3>
                            
                            {quiz.questions.map((q, idx) => {
                                const studentAnswer = attempt.answers?.find((a: any) => a.question_id === q.id);
                                const isCorrect = studentAnswer?.is_correct;

                                return (
                                    <div key={q.id} className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm">
                                        <div className="flex items-start gap-4 mb-6">
                                            <div className={`shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white font-bold mt-0.5 ${isCorrect ? 'bg-emerald-500' : 'bg-red-500'}`}>
                                                {isCorrect ? (
                                                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" /></svg>
                                                ) : (
                                                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M6 18L18 6M6 6l12 12" /></svg>
                                                )}
                                            </div>
                                            <div>
                                                <p className="text-slate-500 text-sm mb-1">السؤال {idx + 1}</p>
                                                <p className="text-lg font-bold text-slate-900 dark:text-white">{q.text}</p>
                                            </div>
                                        </div>

                                        <div className="space-y-3">
                                            {q.options.map((opt: any, i) => {
                                                const isSelected = studentAnswer?.question_option_id === opt.id;
                                                const isActuallyCorrect = opt.is_correct;

                                                let btnClass = 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50';
                                                let iconClass = 'border-slate-300 dark:border-slate-600 text-slate-400';
                                                let iconContent: React.ReactNode = String.fromCharCode(65 + i);

                                                if (isSelected && isActuallyCorrect) {
                                                    // Correct selection
                                                    btnClass = 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300';
                                                    iconClass = 'border-emerald-500 bg-emerald-500 text-white';
                                                    iconContent = <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" /></svg>;
                                                } else if (isSelected && !isActuallyCorrect) {
                                                    // Wrong selection
                                                    btnClass = 'border-red-500 bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-300';
                                                    iconClass = 'border-red-500 bg-red-500 text-white';
                                                    iconContent = <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M6 18L18 6M6 6l12 12" /></svg>;
                                                } else if (!isSelected && isActuallyCorrect) {
                                                    // Missed correct option
                                                    btnClass = 'border-emerald-500 border-dashed text-emerald-700 dark:text-emerald-400';
                                                    iconClass = 'border-emerald-500 text-emerald-600 dark:text-emerald-400';
                                                    iconContent = <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" /></svg>;
                                                }

                                                return (
                                                    <div key={opt.id} className={`w-full text-right px-5 py-3.5 rounded-xl border-2 text-sm font-medium ${btnClass}`}>
                                                        <span className="flex items-center gap-3">
                                                            <span className={`w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0 text-xs font-bold ${iconClass}`}>
                                                                {iconContent}
                                                            </span>
                                                            {opt.text}
                                                        </span>
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>
            </AppLayout>
        );
    }

    // --- State 2: Welcome screen (Not Started) ---
    if (!isStarted) {
        return (
            <AppLayout>
                <Head title={quiz.title} />
                <div className="min-h-[80vh] flex items-center justify-center py-12" dir="rtl">
                    <div className="max-w-md w-full mx-auto px-4 text-center">
                        <div className="w-20 h-20 bg-indigo-100 dark:bg-indigo-900/50 rounded-2xl flex items-center justify-center mx-auto mb-6 text-indigo-600 dark:text-indigo-400">
                            <svg className="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h1 className="text-2xl font-black text-slate-900 dark:text-white mb-4">{quiz.title}</h1>
                        
                        <div className="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700 mb-8 text-right space-y-3">
                            <div className="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-700">
                                <span className="text-slate-500">عدد الأسئلة</span>
                                <span className="font-bold text-slate-900 dark:text-white">{quiz.questions.length} أسئلة</span>
                            </div>
                            {quiz.time_limit_minutes > 0 && (
                                <div className="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-700">
                                    <span className="text-slate-500">الوقت المحدد</span>
                                    <span className="font-bold text-slate-900 dark:text-white">{quiz.time_limit_minutes} دقيقة</span>
                                </div>
                            )}
                            <div className="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-700">
                                <span className="text-slate-500">علامة النجاح</span>
                                <span className="font-bold text-slate-900 dark:text-white">50%</span>
                            </div>
                            <div className="flex items-center gap-2 text-amber-600 dark:text-amber-500 text-sm mt-4 bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg">
                                <svg className="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>انتباه: مغادرة الصفحة أو تحديثها أثناء الاختبار سيؤدي إلى إلغائه واعتباره فاشلاً.</span>
                            </div>
                        </div>

                        <button onClick={startQuiz} className="w-full px-6 py-3.5 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-500 transition-colors shadow-lg shadow-indigo-600/30">
                            بدء الاختبار
                        </button>
                    </div>
                </div>
            </AppLayout>
        );
    }

    // --- State 3: Taking Quiz ---
    const question = quiz.questions[current];
    const total = quiz.questions.length;
    const answered = Object.keys(answers).length;

    return (
        <AppLayout>
            <Head title={quiz.title} />
            <div className="min-h-[80vh] py-12 select-none" dir="rtl" ref={quizRef}>
                <div className="max-w-2xl mx-auto px-4">
                    {/* Header */}
                    <div className="mb-8">
                        <div className="flex items-center justify-between mb-4">
                            <h1 className="text-xl font-black text-slate-900 dark:text-white">{quiz.title}</h1>
                            {timeLeft !== null && (
                                <div className={`flex items-center gap-2 px-3 py-1.5 rounded-lg font-mono font-bold text-lg ${timeLeft < 60 ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'}`}>
                                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {formatTime(timeLeft)}
                                </div>
                            )}
                        </div>
                        <div className="flex items-center gap-4 mt-3">
                            <div className="flex-1 bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                                <div className="bg-indigo-500 h-2 rounded-full transition-all" style={{ width: `${((current + 1) / total) * 100}%` }} />
                            </div>
                            <span className="text-sm text-slate-500 shrink-0">{current + 1} / {total}</span>
                        </div>
                    </div>

                    {/* Question Card */}
                    <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-8 shadow-sm mb-6">
                        <p className="text-lg font-bold text-slate-900 dark:text-white mb-6 select-none">{question.text}</p>
                        <div className="space-y-3">
                            {question.options.map((opt, i) => (
                                <button
                                    key={opt.id}
                                    onClick={() => select(opt.id)}
                                    className={`w-full text-right px-5 py-3.5 rounded-xl border-2 text-sm font-medium transition-all select-none ${
                                        answers[question.id] === opt.id
                                            ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300'
                                            : 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:border-indigo-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'
                                    }`}
                                >
                                    <span className="flex items-center gap-3 pointer-events-none">
                                        <span className={`w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0 text-xs font-bold ${answers[question.id] === opt.id ? 'border-indigo-500 bg-indigo-500 text-white' : 'border-slate-300 dark:border-slate-600 text-slate-400'}`}>
                                            {String.fromCharCode(65 + i)}
                                        </span>
                                        {opt.text}
                                    </span>
                                </button>
                            ))}
                        </div>
                    </div>

                    {/* Navigation */}
                    <div className="flex items-center justify-between">
                        <button
                            onClick={() => setCurrent(c => Math.max(0, c - 1))}
                            disabled={current === 0}
                            className="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold disabled:opacity-40 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                        >
                            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" /></svg>
                            السابق
                        </button>
                        {current < total - 1 ? (
                            <button
                                onClick={() => setCurrent(c => c + 1)}
                                className="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-500 transition-colors"
                            >
                                التالي
                                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" /></svg>
                            </button>
                        ) : (
                            <button
                                onClick={submit}
                                disabled={answered < total || isSubmitting}
                                className="px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {isSubmitting ? 'جاري الإرسال...' : `إرسال الإجابات (${answered}/${total})`}
                            </button>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
