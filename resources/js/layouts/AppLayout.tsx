import { Link, usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import type { PageProps } from '@/types';

export default function AppLayout({ children }: { children: React.ReactNode }) {
    const { auth } = usePage<PageProps>().props;
    const user = auth?.user;
    const [scrolled, setScrolled] = useState(false);
    const [menuOpen, setMenuOpen] = useState(false);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 50);
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    const roles: string[] = (user as any)?.roles ?? [];
    const isAdmin = roles.includes('Super Admin');
    const isInstructor = roles.includes('Instructor');
    const instructorStatus = (user as any)?.instructor_status;

    return (
        <div className="min-h-screen bg-slate-50 dark:bg-slate-900">
            {/* ── Navbar ── */}
            <nav
                className={`fixed top-0 inset-x-0 z-50 transition-all duration-300 ${
                    scrolled
                        ? 'bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 shadow-sm'
                        : 'bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800'
                }`}
            >
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between h-16">
                        {/* Logo */}
                        <Link href="/" className="flex items-center gap-2.5">
                            <div className="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                                <svg className="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                                </svg>
                            </div>
                            <span className="font-black text-slate-900 dark:text-white text-lg">LMS</span>
                        </Link>

                        {/* Desktop nav */}
                        <div className="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600 dark:text-slate-400">
                            <Link href="/courses" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                الدورات
                            </Link>
                            {user && !isInstructor && !isAdmin && (
                                <Link href="/dashboard" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    لوحة التحكم
                                </Link>
                            )}
                            {isInstructor && (
                                <Link href="/instructor/dashboard" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    لوحة تحكم المدرب
                                </Link>
                            )}
                            {isAdmin && (
                                <>
                                    <Link href="/admin/categories" className="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        الفئات
                                    </Link>
                                    <Link href="/admin/instructor-applications" className="relative hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors flex items-center gap-1.5">
                                        طلبات المدربين
                                    </Link>
                                </>
                            )}
                        </div>

                        {/* Desktop CTA */}
                        <div className="hidden md:flex items-center gap-3">
                            {user ? (
                                <div className="flex items-center gap-3">
                                    <div className="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center overflow-hidden shrink-0">
                                        {(user as any)?.avatar_url ? (
                                            <img src={(user as any).avatar_url} alt={user.name} className="w-full h-full object-cover" />
                                        ) : (
                                            <span className="text-xs font-bold text-indigo-600 dark:text-indigo-300">
                                                {user.name?.charAt(0)}
                                            </span>
                                        )}
                                    </div>
                                    <Link
                                        href="/profile"
                                        className="text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                    >
                                        الملف الشخصي
                                    </Link>
                                    <div className="w-px h-4 bg-slate-300 dark:bg-slate-700 mx-1"></div>
                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        className="text-sm font-medium text-slate-500 hover:text-red-500 transition-colors"
                                    >
                                        خروج
                                    </Link>
                                </div>
                            ) : (
                                <>
                                    <Link href="/login" className="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors px-3 py-1.5">
                                        تسجيل الدخول
                                    </Link>
                                    <Link
                                        href="/register"
                                        className="inline-flex items-center gap-2 text-sm font-bold bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl transition-all shadow-lg shadow-indigo-600/25 hover:-translate-y-0.5"
                                    >
                                        ابدأ مجاناً
                                    </Link>
                                </>
                            )}
                        </div>

                        {/* Hamburger */}
                        <button
                            onClick={() => setMenuOpen(!menuOpen)}
                            className="md:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        >
                            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                {menuOpen ? (
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                ) : (
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                                )}
                            </svg>
                        </button>
                    </div>
                </div>

                {/* Mobile menu */}
                {menuOpen && (
                    <div className="md:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 space-y-3">
                        <Link href="/courses" className="block py-2 text-slate-600 dark:text-slate-300 hover:text-indigo-600 font-medium">الدورات</Link>
                        {user && !isInstructor && !isAdmin && <Link href="/dashboard" className="block py-2 text-slate-600 dark:text-slate-300 hover:text-indigo-600 font-medium">لوحة التحكم</Link>}
                        {isInstructor && <Link href="/instructor/dashboard" className="block py-2 text-slate-600 dark:text-slate-300 hover:text-indigo-600 font-medium">لوحة تحكم المدرب</Link>}
                        {!user && (
                            <div className="pt-2 flex flex-col gap-2">
                                <Link href="/login" className="py-2.5 text-center rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-semibold">تسجيل الدخول</Link>
                                <Link href="/register" className="py-2.5 text-center rounded-xl bg-indigo-600 text-white font-bold">ابدأ مجاناً</Link>
                            </div>
                        )}
                    </div>
                )}
            </nav>

            {/* Page Content */}
            <div className="pt-16">
                {/* Pending instructor notice banner */}
                {instructorStatus === 'pending' && (
                    <div className="bg-amber-500 text-white text-sm font-semibold text-center py-2.5 px-4 flex items-center justify-center gap-2" dir="rtl">
                        <svg className="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        طلبك كمدرب قيد المراجعة — سيتم إشعارك بالنتيجة قريباً
                    </div>
                )}
                {instructorStatus === 'rejected' && (
                    <div className="bg-red-600 text-white text-sm font-semibold text-center py-2.5 px-4 flex items-center justify-center gap-2" dir="rtl">
                        <svg className="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        تم رفض طلبك كمدرب —
                        <Link href="/instructor/apply" className="underline hover:no-underline">اضغط هنا للاطلاع على السبب وإعادة التقديم</Link>
                    </div>
                )}
                {children}
            </div>
        </div>
    );
}
