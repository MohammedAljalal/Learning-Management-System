import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '@/layouts/GuestLayout';
import Input from '@/components/ui/Input';
import Button from '@/components/ui/Button';

export default function ForgotPassword({ status }: { status?: string }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/forgot-password');
    };

    return (
        <GuestLayout title="استعادة كلمة المرور" subtitle="أدخل بريدك الإلكتروني لنرسل لك رابط الاستعادة">
            <Head title="استعادة كلمة المرور" />

            {status && <div className="mb-4 font-medium text-sm text-green-500 bg-green-500/10 p-3 rounded-lg text-center">{status}</div>}

            <form onSubmit={submit} className="space-y-5" dir="rtl">
                <Input
                    id="email"
                    label="البريد الإلكتروني"
                    type="email"
                    value={data.email}
                    onChange={e => setData('email', e.target.value)}
                    error={errors.email}
                    placeholder="example@domain.com"
                    autoComplete="username"
                    required
                />

                <Button type="submit" loading={processing} size="lg" className="w-full">
                    إرسال رابط استعادة كلمة المرور
                </Button>

                <p className="text-center text-sm text-slate-400">
                    تذكرت كلمة المرور؟{' '}
                    <Link href="/login" className="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                        العودة لتسجيل الدخول
                    </Link>
                </p>
            </form>
        </GuestLayout>
    );
}
