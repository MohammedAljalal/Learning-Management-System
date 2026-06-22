import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import type { PageProps, Course } from '@/types';

interface Props extends PageProps {
    featuredCourses?: Course[];
}

export default function Welcome({ auth, featuredCourses = [] }: Props) {
    return (
        <AppLayout>
            <Head title="تعلّم بلا حدود" />

            {/* ── HERO ── */}
            <section className="relative min-h-[92vh] flex items-center overflow-hidden"
                style={{ background: 'radial-gradient(ellipse 80% 50% at 50% -5%,rgba(99,102,241,.25) 0%,transparent 65%), radial-gradient(ellipse 40% 40% at 90% 25%,rgba(168,85,247,.15) 0%,transparent 55%), #0a0f1e' }}>

                {/* Grid overlay */}
                <div className="absolute inset-0 pointer-events-none"
                    style={{ backgroundImage: 'linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px)', backgroundSize: '48px 48px' }} />

                {/* Orbs */}
                <div className="absolute top-1/4 right-1/3 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl animate-pulse pointer-events-none" />
                <div className="absolute bottom-1/4 left-1/3 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl pointer-events-none" style={{ animationDelay: '2s' }} />

                <div className="max-w-7xl mx-auto px-6 py-24 w-full grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

                    {/* Text Column */}
                    <div className="space-y-8 order-2 lg:order-1">
                        <div className="inline-flex items-center gap-2.5 bg-white/5 backdrop-blur-xl border border-white/10 px-4 py-2 rounded-full">
                            <span className="flex w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
                            <span className="text-sm text-emerald-300 font-semibold">منصة تعليمية عربية من الجيل القادم</span>
                        </div>

                        <div>
                            <h1 className="text-5xl lg:text-6xl font-black leading-tight text-white">
                                تعلّم مهاراتك<br />
                                <span className="bg-gradient-to-l from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                                    بثقة واحترافية
                                </span>
                            </h1>
                            <p className="mt-5 text-lg text-slate-400 leading-relaxed max-w-lg">
                                آلاف الدورات التدريبية في مجالات التقنية والتصميم والأعمال — مقدّمة من أفضل المدربين العرب بتجربة تعليمية استثنائية.
                            </p>
                        </div>

                        <div className="flex flex-wrap gap-4">
                            <Link href="/courses"
                                className="inline-flex items-center gap-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-base px-7 py-3.5 rounded-2xl transition-all shadow-xl shadow-indigo-600/40 hover:-translate-y-0.5">
                                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                تصفّح الدورات
                            </Link>
                            {auth.user ? (
                                <Link href="/dashboard"
                                    className="inline-flex items-center gap-2.5 bg-white/7 hover:bg-white/12 backdrop-blur-xl border border-white/10 text-white font-semibold text-base px-7 py-3.5 rounded-2xl transition-all">
                                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    لوحة التحكم
                                </Link>
                            ) : (
                                <Link href="/register"
                                    className="inline-flex items-center gap-2.5 bg-white/7 hover:bg-white/12 backdrop-blur-xl border border-white/10 text-white font-semibold text-base px-7 py-3.5 rounded-2xl transition-all">
                                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    إنشاء حساب مجاني
                                </Link>
                            )}
                        </div>

                        {/* Stats */}
                        <div className="flex items-center gap-8 pt-4" style={{ borderTop: '1px solid rgba(255,255,255,0.08)' }}>
                            {[['500+', 'دورة تدريبية'], ['20K+', 'طالب مسجّل'], ['100+', 'مدرب خبير']].map(([val, label], i) => (
                                <div key={i} className="flex items-center gap-8">
                                    {i > 0 && <div style={{ width: 1, height: 40, background: 'rgba(255,255,255,0.08)' }} />}
                                    <div>
                                        <p className="text-3xl font-black" style={{ background: 'linear-gradient(135deg,#fff 30%,#a5b4fc)', WebkitBackgroundClip: 'text', WebkitTextFillColor: 'transparent' }}>{val}</p>
                                        <p className="text-xs text-slate-500 mt-0.5 font-medium">{label}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Visual Card */}
                    <div className="relative flex justify-center items-center order-1 lg:order-2">
                        <div className="animate-[float_6s_ease-in-out_infinite]" style={{ '--tw-translate-y': '0px' } as React.CSSProperties}>
                            <div className="bg-white/5 backdrop-blur-xl border border-white/8 rounded-3xl p-6 w-80 shadow-[0_0_60px_rgba(99,102,241,.22)]">
                                <div className="flex items-center gap-3 mb-5">
                                    <div className="w-11 h-11 rounded-2xl flex items-center justify-center" style={{ background: 'linear-gradient(135deg,#6366f1,#9333ea)' }}>
                                        <svg className="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 10l4.553-2.069A1 1 0 0121 8.857v6.286a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div className="flex-1">
                                        <p className="text-sm font-bold text-white">تطوير الويب الكامل</p>
                                        <p className="text-xs text-slate-400">Ahmed Al-Rashidi</p>
                                    </div>
                                    <span className="text-xs px-2 py-0.5 rounded-full font-semibold" style={{ background: 'rgba(16,185,129,.15)', color: '#34d399', border: '1px solid rgba(16,185,129,.25)' }}>مباشر</span>
                                </div>
                                <div className="mb-5">
                                    <div className="flex justify-between text-xs mb-2">
                                        <span className="text-slate-400 font-medium">تقدّمك في الدورة</span>
                                        <span className="font-bold text-indigo-400">68%</span>
                                    </div>
                                    <div className="w-full rounded-full h-2.5" style={{ background: 'rgba(255,255,255,.08)' }}>
                                        <div className="h-2.5 rounded-full" style={{ width: '68%', background: 'linear-gradient(to left,#6366f1,#a855f7)' }} />
                                    </div>
                                </div>
                                <div className="space-y-2.5">
                                    {['مقدمة في HTML & CSS', 'JavaScript الأساسيات'].map((lesson, i) => (
                                        <div key={i} className="flex items-center gap-3">
                                            <div className="w-5 h-5 rounded-full flex items-center justify-center shrink-0" style={{ background: '#6366f1' }}>
                                                <svg className="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                                                </svg>
                                            </div>
                                            <span className="text-xs text-slate-300 font-medium">{lesson}</span>
                                        </div>
                                    ))}
                                    <div className="flex items-center gap-3">
                                        <div className="w-5 h-5 rounded-full flex items-center justify-center shrink-0" style={{ background: 'rgba(255,255,255,.08)' }}>
                                            <div className="w-1.5 h-1.5 rounded-full bg-slate-600" />
                                        </div>
                                        <span className="text-xs text-slate-500 font-medium">Laravel Framework</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Floating badges */}
                        <div className="absolute -top-5 -start-6 bg-white/7 backdrop-blur-xl border border-white/10 rounded-2xl px-3.5 py-2.5 flex items-center gap-2.5 shadow-2xl shadow-black/50">
                            <div className="w-8 h-8 rounded-xl flex items-center justify-center shrink-0" style={{ background: 'rgba(245,158,11,.15)', border: '1px solid rgba(245,158,11,.3)' }}>
                                <svg className="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clipRule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p className="text-xs font-bold text-white leading-none">شهادة معتمدة</p>
                                <p className="text-xs text-slate-400 mt-0.5">عند الإنجاز</p>
                            </div>
                        </div>

                        <div className="absolute -bottom-5 -end-6 bg-white/7 backdrop-blur-xl border border-white/10 rounded-2xl px-3.5 py-2.5 flex items-center gap-2.5 shadow-2xl shadow-black/50">
                            <div className="w-8 h-8 rounded-xl flex items-center justify-center shrink-0" style={{ background: 'rgba(234,179,8,.15)', border: '1px solid rgba(234,179,8,.3)' }}>
                                <svg className="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clipRule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p className="text-xs font-bold text-yellow-400 leading-none">+250 XP</p>
                                <p className="text-xs text-slate-400 mt-0.5">مكتسبة اليوم</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* ── FEATURES ── */}
            <section id="features" className="py-28 relative" style={{ backgroundColor: '#0a0f1e' }}>
                <div className="absolute top-0 inset-x-0 h-px" style={{ background: 'linear-gradient(to right,transparent,rgba(99,102,241,.3),transparent)' }} />
                <div className="max-w-7xl mx-auto px-6">
                    <div className="text-center mb-16">
                        <p className="inline-flex items-center gap-2 bg-white/5 border border-white/10 px-4 py-1.5 rounded-full text-sm font-semibold mb-5 text-indigo-400">
                            <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                            </svg>
                            لماذا نحن الخيار الأول
                        </p>
                        <h2 className="text-3xl lg:text-5xl font-black text-white mb-4">
                            منصة صُممت للمتعلم<br />
                            <span style={{ background: 'linear-gradient(to left,#818cf8,#c084fc)', WebkitBackgroundClip: 'text', WebkitTextFillColor: 'transparent' }}>العربي الطموح</span>
                        </h2>
                        <p className="text-slate-400 max-w-xl mx-auto leading-relaxed">تجربة تعليمية متكاملة بمعايير دولية — كل ميزة صُممت لمساعدتك على التعلم بفعالية.</p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        {features.map((f, i) => (
                            <div key={i}
                                className="group rounded-2xl p-6 cursor-default transition-all duration-300 hover:-translate-y-1.5"
                                style={{ background: `linear-gradient(135deg,${f.bg} 0%,rgba(255,255,255,.01) 100%)`, border: '1px solid rgba(255,255,255,.06)' }}>
                                <div className="w-12 h-12 rounded-2xl flex items-center justify-center mb-5" style={{ background: f.iconBg, border: `1px solid ${f.iconBorder}` }}>
                                    <svg className="w-6 h-6" style={{ color: f.iconColor }} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.8} d={f.icon} />
                                    </svg>
                                </div>
                                <h3 className="text-base font-bold text-white mb-2">{f.title}</h3>
                                <p className="text-sm text-slate-400 leading-relaxed">{f.desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── CTA ── */}
            <section className="py-24 relative overflow-hidden" style={{ backgroundColor: '#0a0f1e' }}>
                <div className="absolute inset-0" style={{ background: 'linear-gradient(135deg,rgba(49,46,129,.35) 0%,rgba(88,28,135,.25) 100%)' }} />
                <div className="absolute top-0 inset-x-0 h-px" style={{ background: 'linear-gradient(to right,transparent,rgba(99,102,241,.3),transparent)' }} />
                <div className="absolute bottom-0 inset-x-0 h-px" style={{ background: 'linear-gradient(to right,transparent,rgba(168,85,247,.3),transparent)' }} />
                <div className="relative max-w-3xl mx-auto px-6 text-center">
                    <h2 className="text-4xl lg:text-5xl font-black text-white mb-5">
                        ابدأ رحلتك التعليمية<br />
                        <span style={{ background: 'linear-gradient(to left,#818cf8,#c084fc)', WebkitBackgroundClip: 'text', WebkitTextFillColor: 'transparent' }}>اليوم مجاناً</span>
                    </h2>
                    <p className="text-slate-400 text-lg mb-10 max-w-xl mx-auto">آلاف الدورات تنتظرك. سجّل حسابك في ثوانٍ وابدأ التعلم فوراً.</p>
                    <div className="flex flex-wrap justify-center gap-4">
                        {auth.user ? (
                            <Link href="/dashboard" className="inline-flex items-center gap-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-base px-8 py-4 rounded-2xl transition-all shadow-2xl shadow-indigo-600/40 hover:-translate-y-1">
                                لوحة التحكم
                            </Link>
                        ) : (
                            <Link href="/register" className="inline-flex items-center gap-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-base px-8 py-4 rounded-2xl transition-all shadow-2xl shadow-indigo-600/40 hover:-translate-y-1">
                                إنشاء حساب مجاني
                            </Link>
                        )}
                        <Link href="/courses" className="inline-flex items-center gap-2.5 bg-white/7 hover:bg-white/12 border border-white/10 text-white font-semibold text-base px-8 py-4 rounded-2xl transition-all hover:-translate-y-1">
                            تصفّح الدورات
                        </Link>
                    </div>
                </div>
            </section>

            {/* ── FOOTER ── */}
            <footer className="py-10" style={{ background: '#080c18', borderTop: '1px solid rgba(255,255,255,.05)' }}>
                <div className="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div className="flex items-center gap-2.5">
                        <div className="w-8 h-8 rounded-lg flex items-center justify-center" style={{ background: 'linear-gradient(135deg,#6366f1,#9333ea)' }}>
                            <svg className="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                            </svg>
                        </div>
                        <span className="text-sm font-bold text-slate-400">LMS</span>
                    </div>
                    <p className="text-xs text-slate-600">© {new Date().getFullYear()} LMS. جميع الحقوق محفوظة.</p>
                    <div className="flex items-center gap-6 text-xs text-slate-500">
                        <a href="#" className="hover:text-slate-300 transition-colors">الخصوصية</a>
                        <a href="#" className="hover:text-slate-300 transition-colors">الشروط</a>
                        <Link href="/courses" className="hover:text-slate-300 transition-colors">الدورات</Link>
                    </div>
                </div>
            </footer>

            <style>{`
                @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-14px)} }
                .animate-\\[float_6s_ease-in-out_infinite\\] { animation: float 6s ease-in-out infinite; }
            `}</style>
        </AppLayout>
    );
}

const features = [
    { title: 'تعلّم بالترتيب', desc: 'دورات مبنية على هيكل منطقي متدرج من المبتدئ حتى الاحتراف، بمسار واضح لكل مهارة.', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', iconColor: '#818cf8', iconBg: 'rgba(99,102,241,.2)', iconBorder: 'rgba(99,102,241,.3)', bg: 'rgba(99,102,241,.08)' },
    { title: 'فيديوهات عالية الجودة', desc: 'محتوى فيديو احترافي مع مشغّل مخصص يحفظ موضع التوقف تلقائياً ويحمي حقوق المدرب.', icon: 'M15 10l4.553-2.069A1 1 0 0121 8.857v6.286a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', iconColor: '#c084fc', iconBg: 'rgba(168,85,247,.2)', iconBorder: 'rgba(168,85,247,.3)', bg: 'rgba(168,85,247,.08)' },
    { title: 'تتبّع تقدّمك', desc: 'لوحة تحكم ذكية تعرض نسبة إتمامك لكل دورة ومدة التعلم وسجل الإنجازات بشكل مرئي.', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', iconColor: '#34d399', iconBg: 'rgba(16,185,129,.2)', iconBorder: 'rgba(16,185,129,.3)', bg: 'rgba(16,185,129,.08)' },
    { title: 'شهادات موثّقة', desc: 'احصل على شهادة إتمام رسمية بـ QR Code قابل للتحقق الفوري عند إكمال كل دورة بنجاح.', icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', iconColor: '#fbbf24', iconBg: 'rgba(245,158,11,.2)', iconBorder: 'rgba(245,158,11,.3)', bg: 'rgba(245,158,11,.08)' },
    { title: 'اختبارات تفاعلية', desc: 'اختبارات ذكية بعد كل وحدة لقياس فهمك وتعزيز المعلومات مع تغذية راجعة فورية.', icon: 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', iconColor: '#f472b6', iconBg: 'rgba(236,72,153,.2)', iconBorder: 'rgba(236,72,153,.3)', bg: 'rgba(236,72,153,.08)' },
    { title: 'نظام المكافآت', desc: 'اكسب نقاط XP عند كل إنجاز وتنافس مع الآخرين في لوحة المتصدرين لتبقى مُحفَّزاً دائماً.', icon: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', iconColor: '#22d3ee', iconBg: 'rgba(6,182,212,.2)', iconBorder: 'rgba(6,182,212,.3)', bg: 'rgba(6,182,212,.08)' },
];
