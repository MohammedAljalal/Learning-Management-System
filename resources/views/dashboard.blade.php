{{--
  Role-aware dashboard routing.
  Students → Student Dashboard component.
  Instructors → redirect to course manager.
  Admins → redirect to category manager.
--}}
@role('Super Admin')
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">لوحة تحكم المدير</h2>
        </x-slot>
        <div class="py-8" dir="rtl">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm p-8 text-center">
                    <p class="text-slate-700 dark:text-slate-300 mb-4">مرحباً بك في لوحة تحكم المدير.</p>
                    <a href="{{ route('admin.categories') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-6 py-3 rounded-xl transition-colors shadow-lg">
                        إدارة التصنيفات
                    </a>
                </div>
            </div>
        </div>
    </x-app-layout>
@elserole('Instructor')
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">لوحة تحكم المدرب</h2>
        </x-slot>
        <div class="py-8" dir="rtl">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm p-8 text-center">
                    <p class="text-slate-700 dark:text-slate-300 mb-4">مرحباً بك في لوحة تحكم المدرب.</p>
                    <a href="{{ route('instructor.courses') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-6 py-3 rounded-xl transition-colors shadow-lg">
                        إدارة دوراتي
                    </a>
                </div>
            </div>
        </div>
    </x-app-layout>
@else
    {{-- Student Dashboard --}}
    <x-app-layout>
        <livewire:student.dashboard />
    </x-app-layout>
@endrole

