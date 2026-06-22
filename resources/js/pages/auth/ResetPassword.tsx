import { Head, useForm } from '@inertiajs/react';
import { FormEvent, useEffect } from 'react';
import GuestLayout from '@/layouts/GuestLayout';
import Input from '@/components/ui/Input';
import Button from '@/components/ui/Button';

export default function ResetPassword({ token, email }: { token: string, email: string }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        token: token,
        email: email,
        password: '',
        password_confirmation: '',
    });

    useEffect(() => {
        return () => {
            reset('password', 'password_confirmation');
        };
    }, []);

    const submit = (e: FormEvent) => {
        e.preventDefault();
        post('/reset-password');
    };

    return (
        <GuestLayout title="تعيين كلمة مرور جديدة" subtitle="أدخل كلمة المرور الجديدة لحسابك">
            <Head title="تعيين كلمة مرور جديدة" />

            <form onSubmit={submit} className="space-y-5" dir="rtl">
                <Input
                    id="email"
                    label="البريد الإلكتروني"
                    type="email"
                    value={data.email}
                    onChange={e => setData('email', e.target.value)}
                    error={errors.email}
                    readOnly
                    className="opacity-50"
                />

                <Input
                    id="password"
                    label="كلمة المرور الجديدة"
                    type="password"
                    value={data.password}
                    onChange={e => setData('password', e.target.value)}
                    error={errors.password}
                    autoComplete="new-password"
                    required
                />

                <Input
                    id="password_confirmation"
                    label="تأكيد كلمة المرور"
                    type="password"
                    value={data.password_confirmation}
                    onChange={e => setData('password_confirmation', e.target.value)}
                    error={errors.password_confirmation}
                    autoComplete="new-password"
                    required
                />

                <Button type="submit" loading={processing} size="lg" className="w-full">
                    إعادة تعيين كلمة المرور
                </Button>
            </form>
        </GuestLayout>
    );
}
