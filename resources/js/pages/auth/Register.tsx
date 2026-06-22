import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';
import GuestLayout from '@/layouts/GuestLayout';
import Input from '@/components/ui/Input';
import Button from '@/components/ui/Button';
import type { PageProps } from '@/types';

export default function Register(_: PageProps) {
    const [selectedRole, setSelectedRole] = useState<'student' | 'instructor' | null>(null);

    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: '' as 'student' | 'instructor' | '',
    });

    const selectRole = (role: 'student' | 'instructor') => {
        setSelectedRole(role);
        setData('role', role);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/register');
    };

    return (
        <GuestLayout title="إنشاء حساب جديد" subtitle="انضم إلى آلاف المتعلمين العرب">
            <Head title="إنشاء حساب" />
            <form onSubmit={submit} className="space-y-5" dir="rtl">

                {/* Role Selection */}
                <div className="mb-6">
                    <p className="text-sm font-semibold text-slate-300 mb-3">أنا أريد...</p>
                    <div className="grid grid-cols-2 gap-3">
                        {/* Student */}
                        <button
                            type="button"
                            onClick={() => selectRole('student')}
                            className={`relative flex flex-col items-center gap-3 p-4 rounded-2xl border-2 transition-all cursor-pointer text-center ${
                                selectedRole === 'student'
                                    ? 'border-indigo-500 bg-indigo-500/10'
                                    : 'border-slate-700 bg-slate-800/50 hover:border-slate-500'
                            }`}
                        >
                            {selectedRole === 'student' && (
                                <span className="absolute top-2 left-2 w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center">
                                    <svg className="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                            )}
                            <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${selectedRole === 'student' ? 'bg-indigo-500/20 text-indigo-400' : 'bg-slate-700 text-slate-400'}`}>
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                            </div>
                            <div>
                                <p className={`font-bold text-sm ${selectedRole === 'student' ? 'text-indigo-400' : 'text-slate-300'}`}>طالب</p>
                                <p className="text-xs text-slate-500 mt-0.5">تعلم مهارات جديدة</p>
                            </div>
                        </button>

                        {/* Instructor */}
                        <button
                            type="button"
                            onClick={() => selectRole('instructor')}
                            className={`relative flex flex-col items-center gap-3 p-4 rounded-2xl border-2 transition-all cursor-pointer text-center ${
                                selectedRole === 'instructor'
                                    ? 'border-amber-500 bg-amber-500/10'
                                    : 'border-slate-700 bg-slate-800/50 hover:border-slate-500'
                            }`}
                        >
                            {selectedRole === 'instructor' && (
                                <span className="absolute top-2 left-2 w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center">
                                    <svg className="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                            )}
                            <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${selectedRole === 'instructor' ? 'bg-amber-500/20 text-amber-400' : 'bg-slate-700 text-slate-400'}`}>
                                <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m1.636 6.364l.707-.707M12 21v-1m-6.364-1.636l.707-.707M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p className={`font-bold text-sm ${selectedRole === 'instructor' ? 'text-amber-400' : 'text-slate-300'}`}>مدرب</p>
                                <p className="text-xs text-slate-500 mt-0.5">شارك خبرتك ومعرفتك</p>
                            </div>
                        </button>
                    </div>
                    {errors.role && <p className="text-red-400 text-xs mt-2">{errors.role}</p>}
                </div>

                <Input id="name" label="الاسم الكامل" type="text" value={data.name}
                    onChange={e => setData('name', e.target.value)} error={errors.name}
                    placeholder="محمد أحمد" autoComplete="name" required />

                <Input id="email" label="البريد الإلكتروني" type="email" value={data.email}
                    onChange={e => setData('email', e.target.value)} error={errors.email}
                    placeholder="example@domain.com" autoComplete="email" required />

                <Input id="password" label="كلمة المرور" type="password" value={data.password}
                    onChange={e => setData('password', e.target.value)} error={errors.password}
                    placeholder="8 أحرف على الأقل" autoComplete="new-password" required />

                <Input id="password_confirmation" label="تأكيد كلمة المرور" type="password"
                    value={data.password_confirmation}
                    onChange={e => setData('password_confirmation', e.target.value)}
                    error={errors.password_confirmation}
                    placeholder="أعد كتابة كلمة المرور" autoComplete="new-password" required />

                <Button type="submit" loading={processing} size="lg" className="w-full" disabled={!selectedRole}>
                    إنشاء الحساب
                </Button>

                <p className="text-center text-sm text-slate-400">
                    لديك حساب بالفعل؟{' '}
                    <Link href="/login" className="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                        تسجيل الدخول
                    </Link>
                </p>
            </form>
        </GuestLayout>
    );
}
