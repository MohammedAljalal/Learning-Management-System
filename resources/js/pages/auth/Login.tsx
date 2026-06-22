import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '@/layouts/GuestLayout';
import Input from '@/components/ui/Input';
import Button from '@/components/ui/Button';
import type { PageProps } from '@/types';

export default function Login({ auth }: PageProps) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false as boolean,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <GuestLayout title="تسجيل الدخول" subtitle="أهلاً بك مجدداً">
            <Head title="تسجيل الدخول" />
            <form onSubmit={submit} className="space-y-5" dir="rtl">
                <Input
                    id="email"
                    label="البريد الإلكتروني"
                    type="email"
                    value={data.email}
                    onChange={e => setData('email', e.target.value)}
                    error={errors.email}
                    placeholder="example@domain.com"
                    autoComplete="email"
                    required
                />
                <Input
                    id="password"
                    label="كلمة المرور"
                    type="password"
                    value={data.password}
                    onChange={e => setData('password', e.target.value)}
                    error={errors.password}
                    placeholder="••••••••"
                    autoComplete="current-password"
                    required
                />

                <div className="flex items-center justify-between">
                    <label className="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            checked={data.remember}
                            onChange={e => setData('remember', e.target.checked)}
                            className="rounded border-slate-600 bg-slate-800 text-indigo-500 focus:ring-indigo-500"
                        />
                        <span className="text-sm text-slate-300">تذكّرني</span>
                    </label>
                    <Link href="/forgot-password" className="text-sm text-indigo-400 hover:text-indigo-300 transition-colors">
                        نسيت كلمة المرور؟
                    </Link>
                </div>

                <Button type="submit" loading={processing} size="lg" className="w-full">
                    تسجيل الدخول
                </Button>

                <p className="text-center text-sm text-slate-400">
                    ليس لديك حساب؟{' '}
                    <Link href="/register" className="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                        إنشاء حساب مجاني
                    </Link>
                </p>
            </form>
        </GuestLayout>
    );
}
