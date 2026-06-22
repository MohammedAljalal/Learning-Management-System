<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'منصة التعلم') }} — تعلّم بلا حدود</title>
    <meta name="description" content="منصة تعليمية عربية احترافية — دورات عالية الجودة بأيدي أفضل المدربين.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        $cssFile = $manifest['resources/css/app.css']['file'] ?? 'assets/app.css';
        $jsFile  = $manifest['resources/js/app.js']['file']  ?? 'assets/app.js';
    @endphp
    <link rel="stylesheet" href="/build/{{ $cssFile }}">
    <script src="/build/{{ $jsFile }}" defer></script>

    <style>
        html, body, * { font-family: 'Cairo', 'Segoe UI', Arial, sans-serif; }
        .hero-bg {
            background:
                radial-gradient(ellipse 80% 50% at 50% -5%, rgba(99,102,241,.28) 0%, transparent 65%),
                radial-gradient(ellipse 40% 40% at 90% 25%, rgba(168,85,247,.18) 0%, transparent 55%),
                #0a0f1e;
        }
        .grid-bg {
            background-image:
                linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
            background-size: 48px 48px;
        }
        .glass {
            background: rgba(255,255,255,.05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,.08);
        }
        .glass-light {
            background: rgba(255,255,255,.07);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,.10);
        }
        .glow-card { box-shadow: 0 0 60px rgba(99,102,241,.22), 0 0 120px rgba(99,102,241,.08); }
        @keyframes float      { 0%,100%{transform:translateY(0)}  50%{transform:translateY(-14px)} }
        @keyframes pglow      { 0%,100%{opacity:.45} 50%{opacity:.9} }
        @keyframes slideUp    { from{opacity:0;transform:translateY(22px)} to{opacity:1;transform:translateY(0)} }
        .ani-float  { animation: float 6s ease-in-out infinite; }
        .ani-pglow  { animation: pglow 4s ease-in-out infinite; }
        .ani-su0    { animation: slideUp .65s .0s  ease both; }
        .ani-su1    { animation: slideUp .65s .15s ease both; }
        .ani-su2    { animation: slideUp .65s .30s ease both; }
        .ani-su3    { animation: slideUp .65s .45s ease both; }
        /* Feature card hover */
        .fc { transition: transform .25s ease, box-shadow .25s ease; }
        .fc:hover { transform: translateY(-5px); box-shadow: 0 24px 60px rgba(0,0,0,.45), 0 0 40px rgba(99,102,241,.15); }
        /* Stat gradient text */
        .stat-txt {
            background: linear-gradient(135deg,#fff 30%,#a5b4fc);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        ::-webkit-scrollbar { width:5px; }
        ::-webkit-scrollbar-track { background:#0a0f1e; }
        ::-webkit-scrollbar-thumb { background:#312e81; border-radius:99px; }
    </style>
</head>
<body style="background-color:#0a0f1e" class="text-white overflow-x-hidden antialiased">

{{-- ─────────────────────────── NAVBAR ─────────────────────────── --}}
<nav x-data="{ open:false, scrolled:false }"
     @scroll.window="scrolled = window.scrollY > 50"
     :class="scrolled ? 'bg-[#0a0f1e]/95 backdrop-blur-xl border-b border-white/5 shadow-xl shadow-black/40' : ''"
     class="fixed top-0 inset-x-0 z-50 transition-all duration-300">

    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

        {{-- Logo --}}
        <a href="/" class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-600/40">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0z"/>
                </svg>
            </div>
            <span class="text-lg font-black tracking-tight">{{ config('app.name', 'LMS') }}</span>
        </a>

        {{-- Desktop Links --}}
        <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-400">
            <a href="{{ route('courses.catalog') }}" class="hover:text-white transition-colors">الدورات</a>
            <a href="#features" class="hover:text-white transition-colors">المميزات</a>
        </div>

        {{-- Desktop CTA --}}
        <div class="hidden md:flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 text-sm font-bold bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl transition-colors shadow-lg shadow-indigo-600/30">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    لوحة التحكم
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-300 hover:text-white transition-colors px-4 py-2">تسجيل الدخول</a>
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 text-sm font-bold bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-indigo-600/30 hover:-translate-y-0.5">
                    ابدأ مجاناً
                    <svg class="w-4 h-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @endauth
        </div>

        {{-- Hamburger --}}
        <button @click="open=!open" class="md:hidden p-2 rounded-lg glass text-slate-300 hover:text-white">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path x-show="open"  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Mobile Menu — hidden by default even without Alpine --}}
    <div x-show="open" style="display:none"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="md:hidden glass border-t border-white/5 px-6 py-4 space-y-3">
        <a href="{{ route('courses.catalog') }}" class="block py-2 text-slate-300 hover:text-white font-medium">الدورات</a>
        <a href="#features" class="block py-2 text-slate-300 hover:text-white font-medium">المميزات</a>
        @guest
            <div class="pt-2 flex flex-col gap-2">
                <a href="{{ route('login') }}" class="py-2.5 text-center rounded-xl glass text-slate-200 font-semibold">تسجيل الدخول</a>
                <a href="{{ route('register') }}" class="py-2.5 text-center rounded-xl bg-indigo-600 text-white font-bold">ابدأ مجاناً</a>
            </div>
        @endguest
    </div>
</nav>


{{-- ─────────────────────────── HERO ─────────────────────────── --}}
<section class="hero-bg grid-bg min-h-screen flex items-center relative overflow-hidden pt-20">

    {{-- Orbs --}}
    <div class="absolute top-1/4 end-1/3 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl ani-pglow pointer-events-none"></div>
    <div class="absolute bottom-1/4 start-1/3 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl ani-pglow pointer-events-none" style="animation-delay:2s"></div>

    <div class="max-w-7xl mx-auto px-6 py-24 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center w-full">

        {{-- ── Text Column ── --}}
        <div class="space-y-8 order-2 lg:order-1">

            <div class="ani-su0 inline-flex items-center gap-2.5 glass px-4 py-2 rounded-full w-fit">
                <span class="flex w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-sm text-emerald-300 font-semibold">منصة تعليمية عربية من الجيل القادم</span>
            </div>

            <div class="ani-su1">
                <h1 class="text-5xl lg:text-6xl font-black leading-tight text-white">
                    تعلّم مهاراتك<br>
                    <span class="bg-gradient-to-l from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                        بثقة واحترافية
                    </span>
                </h1>
                <p class="mt-5 text-lg text-slate-400 leading-relaxed max-w-lg">
                    آلاف الدورات التدريبية في مجالات التقنية والتصميم والأعمال — مقدّمة من أفضل المدربين العرب بتجربة تعليمية استثنائية.
                </p>
            </div>

            <div class="ani-su2 flex flex-wrap gap-4">
                <a href="{{ route('courses.catalog') }}"
                   class="inline-flex items-center gap-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-base px-7 py-3.5 rounded-2xl transition-all shadow-xl shadow-indigo-600/40 hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    تصفّح الدورات
                </a>
                @guest
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2.5 glass-light hover:bg-white/10 text-white font-semibold text-base px-7 py-3.5 rounded-2xl transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        إنشاء حساب مجاني
                    </a>
                @endguest
            </div>

            <div class="ani-su3 flex items-center gap-8 pt-4" style="border-top: 1px solid rgba(255,255,255,0.08)">
                <div>
                    <p class="text-3xl font-black stat-txt">500+</p>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">دورة تدريبية</p>
                </div>
                <div style="width:1px;height:40px;background:rgba(255,255,255,0.08)"></div>
                <div>
                    <p class="text-3xl font-black stat-txt">20K+</p>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">طالب مسجّل</p>
                </div>
                <div style="width:1px;height:40px;background:rgba(255,255,255,0.08)"></div>
                <div>
                    <p class="text-3xl font-black stat-txt">100+</p>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">مدرب خبير</p>
                </div>
            </div>
        </div>

        {{-- ── Visual Column ── --}}
        <div class="relative flex justify-center items-center ani-float order-1 lg:order-2">
            {{-- Main Card --}}
            <div class="glass rounded-3xl p-6 w-80 glow-card">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shadow-lg" style="background:linear-gradient(135deg,#6366f1,#9333ea)">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.857v6.286a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-white">تطوير الويب الكامل</p>
                        <p class="text-xs text-slate-400">Ahmed Al-Rashidi</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold" style="background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.25)">مباشر</span>
                </div>

                <div class="mb-5">
                    <div class="flex justify-between text-xs mb-2">
                        <span class="text-slate-400 font-medium">تقدّمك في الدورة</span>
                        <span class="font-bold" style="color:#818cf8">68%</span>
                    </div>
                    <div class="w-full rounded-full h-2.5" style="background:rgba(255,255,255,.08)">
                        <div class="h-2.5 rounded-full" style="width:68%;background:linear-gradient(to left,#6366f1,#a855f7)"></div>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0" style="background:#6366f1">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </div>
                        <span class="text-xs text-slate-300 font-medium">مقدمة في HTML & CSS</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0" style="background:#6366f1">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </div>
                        <span class="text-xs text-slate-300 font-medium">JavaScript الأساسيات</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0" style="background:rgba(255,255,255,.08)">
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-600"></div>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">Laravel Framework</span>
                    </div>
                </div>
            </div>

            {{-- Badge: Certificate --}}
            <div class="absolute -top-5 -start-6 glass-light rounded-2xl px-3.5 py-2.5 flex items-center gap-2.5 shadow-2xl shadow-black/50">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3)">
                    <svg class="w-4 h-4" style="color:#fbbf24" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-white leading-none">شهادة معتمدة</p>
                    <p class="text-xs text-slate-400 mt-0.5">عند الإنجاز</p>
                </div>
            </div>

            {{-- Badge: XP --}}
            <div class="absolute -bottom-5 -end-6 glass-light rounded-2xl px-3.5 py-2.5 flex items-center gap-2.5 shadow-2xl shadow-black/50">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(234,179,8,.15);border:1px solid rgba(234,179,8,.3)">
                    <svg class="w-4 h-4" style="color:#facc15" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold leading-none" style="color:#facc15">+250 XP</p>
                    <p class="text-xs text-slate-400 mt-0.5">مكتسبة اليوم</p>
                </div>
            </div>

            {{-- Badge: Rank --}}
            <div class="absolute top-1/2 -end-8 -translate-y-1/2 glass-light rounded-2xl px-3 py-2.5 flex items-center gap-2 shadow-xl shadow-black/40">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background:rgba(168,85,247,.15);border:1px solid rgba(168,85,247,.3)">
                    <svg class="w-4 h-4" style="color:#c084fc" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-white leading-none">#12</p>
                    <p class="text-[10px] text-slate-400">متصدرين</p>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ─────────────────────────── FEATURES ─────────────────────────── --}}
<section id="features" style="background-color:#0a0f1e" class="py-28 relative">
    <div class="absolute top-0 inset-x-0 h-px" style="background:linear-gradient(to right,transparent,rgba(99,102,241,.3),transparent)"></div>

    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <p class="inline-flex items-center gap-2 glass px-4 py-1.5 rounded-full text-sm font-semibold mb-5" style="color:#818cf8">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                لماذا نحن الخيار الأول
            </p>
            <h2 class="text-3xl lg:text-5xl font-black text-white mb-4">
                منصة صُممت للمتعلم<br>
                <span style="background:linear-gradient(to left,#818cf8,#c084fc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">العربي الطموح</span>
            </h2>
            <p class="text-slate-400 max-w-xl mx-auto leading-relaxed">تجربة تعليمية متكاملة بمعايير دولية — كل ميزة صُممت لمساعدتك على التعلم بفعالية.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            {{-- Card 1 --}}
            <div class="fc glass rounded-2xl p-6 cursor-default" style="background:linear-gradient(135deg,rgba(99,102,241,.12) 0%,rgba(99,102,241,.02) 100%)">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5" style="background:rgba(99,102,241,.2);border:1px solid rgba(99,102,241,.3)">
                    <svg class="w-6 h-6" style="color:#818cf8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white mb-2">تعلّم بالترتيب</h3>
                <p class="text-sm text-slate-400 leading-relaxed">دورات مبنية على هيكل منطقي متدرج من المبتدئ حتى الاحتراف، بمسار واضح لكل مهارة.</p>
            </div>

            {{-- Card 2 --}}
            <div class="fc glass rounded-2xl p-6 cursor-default" style="background:linear-gradient(135deg,rgba(168,85,247,.12) 0%,rgba(168,85,247,.02) 100%)">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5" style="background:rgba(168,85,247,.2);border:1px solid rgba(168,85,247,.3)">
                    <svg class="w-6 h-6" style="color:#c084fc" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 10l4.553-2.069A1 1 0 0121 8.857v6.286a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white mb-2">فيديوهات عالية الجودة</h3>
                <p class="text-sm text-slate-400 leading-relaxed">محتوى فيديو احترافي مع مشغّل مخصص يحفظ موضع التوقف تلقائياً ويحمي حقوق المدرب.</p>
            </div>

            {{-- Card 3 --}}
            <div class="fc glass rounded-2xl p-6 cursor-default" style="background:linear-gradient(135deg,rgba(16,185,129,.12) 0%,rgba(16,185,129,.02) 100%)">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5" style="background:rgba(16,185,129,.2);border:1px solid rgba(16,185,129,.3)">
                    <svg class="w-6 h-6" style="color:#34d399" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white mb-2">تتبّع تقدّمك</h3>
                <p class="text-sm text-slate-400 leading-relaxed">لوحة تحكم ذكية تعرض نسبة إتمامك لكل دورة ومدة التعلم وسجل الإنجازات بشكل مرئي.</p>
            </div>

            {{-- Card 4 --}}
            <div class="fc glass rounded-2xl p-6 cursor-default" style="background:linear-gradient(135deg,rgba(245,158,11,.12) 0%,rgba(245,158,11,.02) 100%)">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5" style="background:rgba(245,158,11,.2);border:1px solid rgba(245,158,11,.3)">
                    <svg class="w-6 h-6" style="color:#fbbf24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white mb-2">شهادات موثّقة</h3>
                <p class="text-sm text-slate-400 leading-relaxed">احصل على شهادة إتمام رسمية بـ QR Code قابل للتحقق الفوري عند إكمال كل دورة بنجاح.</p>
            </div>

            {{-- Card 5 --}}
            <div class="fc glass rounded-2xl p-6 cursor-default" style="background:linear-gradient(135deg,rgba(236,72,153,.12) 0%,rgba(236,72,153,.02) 100%)">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5" style="background:rgba(236,72,153,.2);border:1px solid rgba(236,72,153,.3)">
                    <svg class="w-6 h-6" style="color:#f472b6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white mb-2">اختبارات تفاعلية</h3>
                <p class="text-sm text-slate-400 leading-relaxed">اختبارات ذكية بعد كل وحدة لقياس فهمك وتعزيز المعلومات مع تغذية راجعة فورية.</p>
            </div>

            {{-- Card 6 --}}
            <div class="fc glass rounded-2xl p-6 cursor-default" style="background:linear-gradient(135deg,rgba(6,182,212,.12) 0%,rgba(6,182,212,.02) 100%)">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5" style="background:rgba(6,182,212,.2);border:1px solid rgba(6,182,212,.3)">
                    <svg class="w-6 h-6" style="color:#22d3ee" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white mb-2">نظام المكافآت</h3>
                <p class="text-sm text-slate-400 leading-relaxed">اكسب نقاط XP عند كل إنجاز وتنافس مع الآخرين في لوحة المتصدرين لتبقى مُحفَّزاً دائماً.</p>
            </div>

        </div>
    </div>
</section>


{{-- ─────────────────────────── CTA BANNER ─────────────────────────── --}}
<section class="py-24 relative overflow-hidden" style="background-color:#0a0f1e">
    <div class="absolute inset-0" style="background:linear-gradient(135deg,rgba(49,46,129,.35) 0%,rgba(88,28,135,.25) 100%)"></div>
    <div class="absolute inset-0 grid-bg opacity-30"></div>
    <div class="absolute top-0 inset-x-0 h-px" style="background:linear-gradient(to right,transparent,rgba(99,102,241,.3),transparent)"></div>
    <div class="absolute bottom-0 inset-x-0 h-px" style="background:linear-gradient(to right,transparent,rgba(168,85,247,.3),transparent)"></div>

    <div class="relative max-w-3xl mx-auto px-6 text-center">
        <h2 class="text-4xl lg:text-5xl font-black text-white mb-5">
            ابدأ رحلتك التعليمية<br>
            <span style="background:linear-gradient(to left,#818cf8,#c084fc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">اليوم مجاناً</span>
        </h2>
        <p class="text-slate-400 text-lg mb-10 max-w-xl mx-auto">آلاف الدورات تنتظرك. سجّل حسابك في ثوانٍ وابدأ التعلم فوراً.</p>
        <div class="flex flex-wrap justify-center gap-4">
            @guest
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-base px-8 py-4 rounded-2xl transition-all shadow-2xl shadow-indigo-600/40 hover:-translate-y-1">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    إنشاء حساب مجاني
                </a>
            @endguest
            <a href="{{ route('courses.catalog') }}"
               class="inline-flex items-center gap-2.5 glass-light hover:bg-white/10 text-white font-semibold text-base px-8 py-4 rounded-2xl transition-all hover:-translate-y-1">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                تصفّح الدورات
            </a>
        </div>
    </div>
</section>


{{-- ─────────────────────────── FOOTER ─────────────────────────── --}}
<footer class="py-10" style="background:#080c18;border-top:1px solid rgba(255,255,255,.05)">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:linear-gradient(135deg,#6366f1,#9333ea)">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-slate-400">{{ config('app.name') }}</span>
        </div>
        <p class="text-xs text-slate-600">© {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة.</p>
        <div class="flex items-center gap-6 text-xs text-slate-500">
            <a href="#" class="hover:text-slate-300 transition-colors">الخصوصية</a>
            <a href="#" class="hover:text-slate-300 transition-colors">الشروط</a>
            <a href="{{ route('courses.catalog') }}" class="hover:text-slate-300 transition-colors">الدورات</a>
        </div>
    </div>
</footer>

</body>
</html>
