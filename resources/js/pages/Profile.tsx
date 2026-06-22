import { Head, useForm, router } from '@inertiajs/react';
import { useRef, useState } from 'react';
import AppLayout from '@/layouts/AppLayout';
import Input from '@/components/ui/Input';
import Button from '@/components/ui/Button';
import type { PageProps } from '@/types';

export default function Profile({ auth }: PageProps) {
    const user = auth.user as any;
    const fileRef = useRef<HTMLInputElement>(null);
    const [avatarPreview, setAvatarPreview] = useState<string | null>(null);

    const { data, setData, post, processing, errors, recentlySuccessful } = useForm({
        name:   user?.name ?? '',
        email:  user?.email ?? '',
        bio:    user?.bio ?? '',
        avatar: null as File | null,
        _method: 'PATCH',
    });

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const handleAvatarChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;
        setData('avatar', file);
        setAvatarPreview(URL.createObjectURL(file));
    };

    const updateProfile = (e: React.FormEvent) => {
        e.preventDefault();
        post('/profile', { forceFormData: true });
    };

    const updatePassword = (e: React.FormEvent) => {
        e.preventDefault();
        passwordForm.put('/password', {
            preserveScroll: true,
            onSuccess: () => passwordForm.reset(),
        });
    };

    const avatarUrl = avatarPreview ?? (user?.avatar ? `/storage/${user.avatar}` : null);
    const initials  = user?.name?.charAt(0)?.toUpperCase() ?? '?';

    return (
        <AppLayout>
            <Head title="الملف الشخصي" />
            <div className="py-12 min-h-[80vh]" dir="rtl">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

                    <div className="mb-2">
                        <h1 className="text-2xl font-black text-slate-900 dark:text-white">الملف الشخصي</h1>
                        <p className="text-slate-500 dark:text-slate-400 text-sm mt-1">تحديث معلوماتك الشخصية وصورتك</p>
                    </div>

                    {/* Profile Card */}
                    <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm p-6 sm:p-8">
                        <h2 className="text-lg font-bold text-slate-900 dark:text-white mb-6">المعلومات الشخصية</h2>
                        <form onSubmit={updateProfile} className="space-y-6">
                            {/* Avatar Section */}
                            <div className="flex items-center gap-5">
                                {/* Avatar Preview */}
                                <div className="relative shrink-0">
                                    <div className="w-20 h-20 rounded-2xl overflow-hidden bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                                        {avatarUrl ? (
                                            <img
                                                src={avatarUrl}
                                                alt={user?.name}
                                                className="w-full h-full object-cover"
                                            />
                                        ) : (
                                            <span className="text-2xl font-black text-white">{initials}</span>
                                        )}
                                    </div>
                                    {/* Edit overlay */}
                                    <button
                                        type="button"
                                        onClick={() => fileRef.current?.click()}
                                        className="absolute -bottom-1.5 -end-1.5 w-7 h-7 bg-indigo-600 hover:bg-indigo-500 text-white rounded-full flex items-center justify-center shadow-md transition-colors"
                                        title="تغيير الصورة"
                                    >
                                        <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                </div>

                                <div>
                                    <p className="font-bold text-slate-900 dark:text-white">{user?.name}</p>
                                    <p className="text-sm text-slate-500 dark:text-slate-400">{user?.email}</p>
                                    <button
                                        type="button"
                                        onClick={() => fileRef.current?.click()}
                                        className="mt-2 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline"
                                    >
                                        تغيير الصورة الشخصية
                                    </button>
                                    {errors.avatar && <p className="text-xs text-red-500 mt-1">{errors.avatar}</p>}
                                </div>

                                <input
                                    ref={fileRef}
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    onChange={handleAvatarChange}
                                    className="hidden"
                                />
                            </div>

                            {/* Name & Email */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-xl">
                                <Input
                                    id="name"
                                    label="الاسم الكامل"
                                    type="text"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    error={errors.name}
                                />
                                <Input
                                    id="email"
                                    label="البريد الإلكتروني"
                                    type="email"
                                    value={data.email}
                                    onChange={e => setData('email', e.target.value)}
                                    error={errors.email}
                                />
                            </div>

                            {/* Bio */}
                            <div className="max-w-xl">
                                <label htmlFor="bio" className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                    نبذة شخصية
                                </label>
                                <textarea
                                    id="bio"
                                    rows={3}
                                    value={data.bio}
                                    onChange={e => setData('bio', e.target.value)}
                                    placeholder="اكتب نبذة قصيرة عن نفسك وخبراتك..."
                                    className="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none"
                                />
                                {errors.bio && <p className="text-xs text-red-500 mt-1">{errors.bio}</p>}
                            </div>

                            <div className="flex items-center gap-4">
                                <Button type="submit" loading={processing}>حفظ التعديلات</Button>
                                {recentlySuccessful && (
                                    <span className="text-sm text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                        </svg>
                                        تم الحفظ بنجاح
                                    </span>
                                )}
                            </div>
                        </form>
                    </div>

                    {/* Update Password */}
                    <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm p-6 sm:p-8">
                        <h2 className="text-lg font-bold text-slate-900 dark:text-white mb-6">تحديث كلمة المرور</h2>
                        <form onSubmit={updatePassword} className="space-y-6 max-w-xl">
                            <Input
                                id="current_password"
                                label="كلمة المرور الحالية"
                                type="password"
                                value={passwordForm.data.current_password}
                                onChange={e => passwordForm.setData('current_password', e.target.value)}
                                error={passwordForm.errors.current_password}
                            />
                            <Input
                                id="password"
                                label="كلمة المرور الجديدة"
                                type="password"
                                value={passwordForm.data.password}
                                onChange={e => passwordForm.setData('password', e.target.value)}
                                error={passwordForm.errors.password}
                            />
                            <Input
                                id="password_confirmation"
                                label="تأكيد كلمة المرور الجديدة"
                                type="password"
                                value={passwordForm.data.password_confirmation}
                                onChange={e => passwordForm.setData('password_confirmation', e.target.value)}
                                error={passwordForm.errors.password_confirmation}
                            />
                            <div className="flex items-center gap-4">
                                <Button type="submit" loading={passwordForm.processing}>تغيير كلمة المرور</Button>
                                {passwordForm.recentlySuccessful && (
                                    <span className="text-sm text-emerald-600 dark:text-emerald-400 font-medium">تم التغيير بنجاح.</span>
                                )}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
