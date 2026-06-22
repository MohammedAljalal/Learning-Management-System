<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تحقق من الشهادة | {{ config('app.name') }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Cairo', sans-serif; }</style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto w-20 h-20 bg-emerald-100 dark:bg-emerald-900/40 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">شهادة موثقة ومعتمدة</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                هذه الشهادة رسمية ومصدرها {{ config('app.name') }}.
            </p>
        </div>

        <div class="bg-white dark:bg-slate-900 shadow-xl rounded-2xl p-8 border border-slate-100 dark:border-slate-800">
            <div class="space-y-6">
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1">اسم الطالب</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $certificate->user->name }}</p>
                </div>
                
                <div class="border-t border-slate-100 dark:border-slate-800 pt-4">
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1">الدورة التدريبية</p>
                    <p class="text-lg font-semibold text-slate-800 dark:text-slate-200">{{ $certificate->course->title }}</p>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-800 pt-4 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1">تاريخ الإصدار</p>
                        <p class="font-medium">{{ $certificate->issued_at->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider mb-1">المدرب</p>
                        <p class="font-medium">{{ $certificate->course->instructor?->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 mt-6 text-center">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">رقم التحقق المرجعي (UUID)</p>
                    <code class="text-xs font-mono text-indigo-600 dark:text-indigo-400 break-all">{{ $certificate->uuid }}</code>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="/" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                العودة إلى الصفحة الرئيسية
            </a>
        </div>
    </div>

</body>
</html>
