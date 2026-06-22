import { Head, Link, router } from '@inertiajs/react';
import { useState, useRef, useEffect } from 'react';
import AppLayout from '@/layouts/AppLayout';
import AIChatbot from '@/components/AIChatbot';
import type { PageProps, Course, Section, Lesson } from '@/types';

interface Props extends PageProps {
    course: Course;
    sections: Section[];
    currentLesson: Lesson;
    videoUrl: string | null;
    savedPosition: number;
    unlockedLessonIds: number[];
    completedLessonIds: number[];
}

export default function CoursePlayer({ course, sections, currentLesson, videoUrl, savedPosition, unlockedLessonIds, completedLessonIds }: Props) {
    const [sidebarOpen, setSidebarOpen] = useState(true);
    const [activeTab, setActiveTab] = useState('overview');
    const [isCompleting, setIsCompleting] = useState(false);
    const [videoCompleted, setVideoCompleted] = useState(false);
    const [maxTimeWatched, setMaxTimeWatched] = useState(savedPosition || 0);
    
    const videoRef = useRef<HTMLVideoElement>(null);

    useEffect(() => {
        // Reset component state when navigating to a new lesson
        setIsCompleting(false);
        setVideoCompleted(!videoUrl); // Auto complete if no video
        setMaxTimeWatched(savedPosition || 0);
        
        if (videoRef.current && savedPosition > 0) {
            videoRef.current.currentTime = savedPosition;
        }
    }, [currentLesson.id, videoUrl, savedPosition]);

    const handleTimeUpdate = () => {
        if (!videoRef.current) return;
        const current = videoRef.current.currentTime;
        
        // Prevent seeking forward (Loophole fix)
        // TEMPORARILY DISABLED FOR TESTING
        /*
        if (current > maxTimeWatched + 2 && !completedLessonIds.includes(currentLesson.id)) {
            videoRef.current.currentTime = maxTimeWatched;
        } else if (current > maxTimeWatched) {
            setMaxTimeWatched(current);
        }
        */
    };

    const handleVideoEnded = () => {
        setVideoCompleted(true);
    };

    const markCompleted = () => {
        if (isCompleting) return;
        setIsCompleting(true);
        router.post(`/courses/${course.slug}/learn/${currentLesson.id}/complete`);
    };

    return (
        <div className="min-h-screen bg-slate-950 text-slate-300 font-sans flex flex-col" dir="rtl">
            <Head title={`${currentLesson.title} | ${course.title}`} />
            
            {/* Top Navigation */}
            <nav className="h-16 bg-slate-900 border-b border-slate-800 flex items-center justify-between px-4 sm:px-6 shrink-0 z-10 relative">
                <div className="flex items-center gap-4">
                    <Link href={`/courses/${course.slug}`} className="text-slate-400 hover:text-white transition-colors flex items-center justify-center w-10 h-10 rounded-full hover:bg-slate-800">
                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div className="hidden sm:block w-px h-6 bg-slate-800"></div>
                    <div className="flex flex-col">
                        <span className="text-xs text-slate-500">{course.title}</span>
                        <h1 className="text-sm font-bold text-white line-clamp-1">{currentLesson.title}</h1>
                    </div>
                </div>
                
                <div className="flex items-center gap-3">
                    <button 
                        onClick={markCompleted}
                        disabled={isCompleting || (!videoCompleted && !completedLessonIds.includes(currentLesson.id))}
                        className="hidden sm:flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold px-4 py-2 rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {isCompleting ? 'جاري الحفظ...' : (!videoCompleted && !completedLessonIds.includes(currentLesson.id)) ? 'يجب إكمال المشاهدة' : 'اكتمال ومتابعة'}
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    
                    <button onClick={() => setSidebarOpen(!sidebarOpen)} className="text-slate-400 hover:text-white transition-colors p-2 rounded-lg hover:bg-slate-800 lg:hidden">
                        <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </nav>

            {/* Main Layout */}
            <div className="flex-1 flex overflow-hidden relative">
                
                {/* Video Area */}
                <div className={`flex-1 flex flex-col overflow-y-auto transition-all duration-300 ${sidebarOpen ? 'lg:mr-[350px]' : ''}`}>
                    {/* Video Container */}
                    <div className="w-full bg-black relative aspect-video flex items-center justify-center">
                        {videoUrl ? (
                            <video
                                ref={videoRef}
                                src={videoUrl}
                                controls
                                onTimeUpdate={handleTimeUpdate}
                                onEnded={handleVideoEnded}
                                controlsList="nodownload"
                                className="w-full h-full"
                            />
                        ) : (
                            <div className="flex flex-col items-center text-slate-500">
                                <svg className="w-16 h-16 mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <p>محتوى الفيديو غير متوفر</p>
                            </div>
                        )}
                    </div>

                    {/* Content Below Video */}
                    <div className="max-w-5xl mx-auto w-full p-6 sm:p-8">
                        {/* Mobile Complete Button */}
                        <div className="sm:hidden mb-6">
                            <button 
                                onClick={markCompleted}
                                disabled={isCompleting || (!videoCompleted && !completedLessonIds.includes(currentLesson.id))}
                                className="w-full flex justify-center items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {isCompleting ? 'جاري الحفظ...' : (!videoCompleted && !completedLessonIds.includes(currentLesson.id)) ? 'يجب إكمال المشاهدة' : 'اكتمال ومتابعة'}
                                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

                        {/* Tabs Navigation */}
                        <div className="flex items-center gap-6 border-b border-slate-800 mb-8 overflow-x-auto pb-px">
                            <button 
                                onClick={() => setActiveTab('overview')}
                                className={`pb-4 text-sm font-bold whitespace-nowrap transition-colors relative ${activeTab === 'overview' ? 'text-white' : 'text-slate-500 hover:text-slate-300'}`}
                            >
                                نظرة عامة
                                {activeTab === 'overview' && <div className="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-500 rounded-t-full"></div>}
                            </button>
                            <button 
                                onClick={() => setActiveTab('qna')}
                                className={`pb-4 text-sm font-bold whitespace-nowrap transition-colors relative ${activeTab === 'qna' ? 'text-white' : 'text-slate-500 hover:text-slate-300'}`}
                            >
                                أسئلة وأجوبة
                                {activeTab === 'qna' && <div className="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-500 rounded-t-full"></div>}
                            </button>
                            <button 
                                onClick={() => setActiveTab('resources')}
                                className={`pb-4 text-sm font-bold whitespace-nowrap transition-colors relative ${activeTab === 'resources' ? 'text-white' : 'text-slate-500 hover:text-slate-300'}`}
                            >
                                الملحقات
                                {activeTab === 'resources' && <div className="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-500 rounded-t-full"></div>}
                            </button>
                        </div>

                        {/* Tab Contents */}
                        <div className="prose prose-invert prose-indigo max-w-none">
                            {activeTab === 'overview' && (
                                <div className="space-y-6">
                                    <h2 className="text-2xl font-bold text-white mb-4">حول هذا الدرس</h2>
                                    <div dangerouslySetInnerHTML={{ __html: currentLesson.content || '<p class="text-slate-400">لا يوجد وصف متاح لهذا الدرس.</p>' }} />
                                </div>
                            )}

                            {activeTab === 'qna' && (
                                <div className="text-center py-12">
                                    <svg className="w-12 h-12 text-slate-700 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <h3 className="text-lg font-bold text-white mb-2">منتدى الأسئلة والأجوبة</h3>
                                    <p className="text-slate-500">هذه الميزة قيد التطوير وستتوفر قريباً.</p>
                                </div>
                            )}

                            {activeTab === 'resources' && (
                                <div className="text-center py-12">
                                    <svg className="w-12 h-12 text-slate-700 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 className="text-lg font-bold text-white mb-2">الملفات المرفقة</h3>
                                    <p className="text-slate-500">لا توجد ملفات مرفقة مع هذا الدرس.</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Sidebar (Curriculum) */}
                <div className={`fixed inset-y-0 right-0 lg:top-16 lg:bottom-0 w-full sm:w-[350px] bg-slate-900 border-l border-slate-800 transform transition-transform duration-300 ease-in-out z-20 flex flex-col ${sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:hidden'}`}>
                    
                    <div className="p-4 border-b border-slate-800 flex items-center justify-between shrink-0 bg-slate-900">
                        <h2 className="font-bold text-white">محتوى الدورة</h2>
                        <button onClick={() => setSidebarOpen(false)} className="lg:hidden text-slate-400 hover:text-white p-2">
                            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div className="flex-1 overflow-y-auto custom-scrollbar">
                        {sections?.map((section, sIdx) => (
                            <div key={section.id} className="border-b border-slate-800">
                                <div className="p-4 bg-slate-900/50 sticky top-0 backdrop-blur-md z-10 border-b border-slate-800/50">
                                    <h3 className="font-bold text-slate-300 text-sm">
                                        القسم {sIdx + 1}: {section.title}
                                    </h3>
                                </div>
                                <div className="divide-y divide-slate-800/50">
                                    {section.lessons?.map((lesson, lIdx) => {
                                        const isCurrent = lesson.id === currentLesson.id;
                                        const isCompleted = completedLessonIds?.includes(lesson.id);
                                        const isUnlocked = unlockedLessonIds?.includes(lesson.id);

                                        return (
                                            <Link
                                                key={lesson.id}
                                                href={isUnlocked ? `/courses/${course.slug}/learn/${lesson.id}` : '#'}
                                                className={`flex p-4 transition-colors gap-3 ${
                                                    isCurrent 
                                                    ? 'bg-indigo-600/10 border-l-4 border-indigo-500' 
                                                    : isUnlocked 
                                                        ? 'hover:bg-slate-800/50 cursor-pointer' 
                                                        : 'opacity-50 cursor-not-allowed'
                                                }`}
                                            >
                                                <div className="shrink-0 mt-0.5">
                                                    {isCompleted ? (
                                                        <div className="w-5 h-5 rounded-full bg-emerald-500 flex items-center justify-center text-white">
                                                            <svg className="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </div>
                                                    ) : isCurrent ? (
                                                        <div className="w-5 h-5 rounded-full border-2 border-indigo-500 flex items-center justify-center">
                                                            <div className="w-2 h-2 rounded-full bg-indigo-500"></div>
                                                        </div>
                                                    ) : isUnlocked ? (
                                                        <div className="w-5 h-5 rounded-full border-2 border-slate-600 flex items-center justify-center">
                                                            <svg className="w-2.5 h-2.5 text-slate-600 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                                                            </svg>
                                                        </div>
                                                    ) : (
                                                        <div className="w-5 h-5 flex items-center justify-center text-slate-600">
                                                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                            </svg>
                                                        </div>
                                                    )}
                                                </div>
                                                <div className="flex-1">
                                                    <p className={`text-sm font-medium leading-relaxed ${isCurrent ? 'text-white' : 'text-slate-300'}`}>
                                                        {lIdx + 1}. {lesson.title}
                                                    </p>
                                                </div>
                                            </Link>
                                        );
                                    })}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

            </div>

            <style>{`
                .custom-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }
                .custom-scrollbar::-webkit-scrollbar-track {
                    background: transparent;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background-color: #334155;
                    border-radius: 20px;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background-color: #475569;
                }
            `}</style>
            
            {/* AI Chatbot Widget */}
            <AIChatbot courseId={course.id} lessonId={currentLesson.id} />
        </div>
    );
}
