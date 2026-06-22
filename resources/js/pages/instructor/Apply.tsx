import { Head, router, useForm } from '@inertiajs/react';
import { useState, useRef } from 'react';
import AppLayout from '@/layouts/AppLayout';
import type { PageProps } from '@/types';

interface Props extends PageProps {
    status: string | null;
    rejection_reason: string | null;
}

const steps = [
    { id: 1, title: 'البيانات الأساسية', desc: 'معلوماتك الشخصية والمهنية' },
    { id: 2, title: 'وجه البطاقة', desc: 'صورة الوجه الأمامي لبطاقة الهوية' },
    { id: 3, title: 'ظهر البطاقة', desc: 'صورة الوجه الخلفي لبطاقة الهوية' },
    { id: 4, title: 'صورة السيلفي', desc: 'سيلفي وأنت تمسك بطاقة الهوية' },
    { id: 5, title: 'مراجعة وتقديم', desc: 'راجع بياناتك وقدّم الطلب' },
];

function StepIndicator({ current }: { current: number }) {
    return (
        <div className="flex items-center justify-center mb-10 gap-0">
            {steps.map((step, idx) => (
                <div key={step.id} className="flex items-center">
                    <div className="flex flex-col items-center">
                        <div className={`w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm transition-all ${
                            current > step.id ? 'bg-emerald-500 text-white' :
                            current === step.id ? 'bg-indigo-600 text-white ring-4 ring-indigo-500/20' :
                            'bg-slate-200 dark:bg-slate-700 text-slate-400'
                        }`}>
                            {current > step.id ? (
                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                                </svg>
                            ) : step.id}
                        </div>
                        <span className={`text-xs mt-1 hidden md:block text-center max-w-[80px] ${current === step.id ? 'text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-400'}`}>
                            {step.title}
                        </span>
                    </div>
                    {idx < steps.length - 1 && (
                        <div className={`h-0.5 w-10 md:w-16 mx-1 mb-5 transition-all ${current > step.id ? 'bg-emerald-400' : 'bg-slate-200 dark:bg-slate-700'}`} />
                    )}
                </div>
            ))}
        </div>
    );
}

function ImageUploadBox({ label, desc, fieldName, currentPath, onUploaded }: {
    label: string; desc: string; fieldName: string; currentPath?: string; onUploaded: () => void;
}) {
    const [preview, setPreview] = useState<string | null>(null);
    const [uploading, setUploading] = useState(false);
    const [done, setDone] = useState(!!currentPath);
    const inputRef = useRef<HTMLInputElement>(null);

    const handleFile = (file: File) => {
        const objectUrl = URL.createObjectURL(file);
        setPreview(objectUrl);
        setUploading(true);

        const formData = new FormData();
        formData.append('image', file);
        formData.append('type', fieldName);

        router.post('/instructor/apply/upload', formData, {
            forceFormData: true,
            onSuccess: () => { setDone(true); setUploading(false); onUploaded(); },
            onError: () => { setUploading(false); },
        });
    };

    return (
        <div className="space-y-3">
            <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300">{label}</label>
            <p className="text-xs text-slate-500">{desc}</p>

            <div
                onClick={() => inputRef.current?.click()}
                className={`relative border-2 border-dashed rounded-2xl p-8 text-center cursor-pointer transition-all ${
                    done ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20' :
                    'border-slate-300 dark:border-slate-600 hover:border-indigo-400 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/10'
                }`}
            >
                <input ref={inputRef} type="file" accept="image/*" className="hidden" onChange={e => e.target.files?.[0] && handleFile(e.target.files[0])} />
                
                {preview ? (
                    <img src={preview} alt="Preview" className="h-40 mx-auto rounded-xl object-cover shadow" />
                ) : (
                    <div className="flex flex-col items-center gap-3">
                        <div className={`w-14 h-14 rounded-2xl flex items-center justify-center ${done ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-slate-100 dark:bg-slate-800'}`}>
                            {done ? (
                                <svg className="w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                </svg>
                            ) : (
                                <svg className="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            )}
                        </div>
                        <p className="text-sm font-medium text-slate-600 dark:text-slate-400">
                            {done ? 'تم الرفع بنجاح. انقر لتغيير الصورة' : 'انقر لرفع الصورة'}
                        </p>
                        <p className="text-xs text-slate-400">JPG, PNG, WEBP حتى 5MB</p>
                    </div>
                )}

                {uploading && (
                    <div className="absolute inset-0 bg-white/80 dark:bg-slate-900/80 rounded-2xl flex items-center justify-center">
                        <div className="flex items-center gap-2 text-indigo-600">
                            <svg className="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            <span className="text-sm font-semibold">جاري الرفع...</span>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}

export default function InstructorApply({ status, rejection_reason, auth }: Props) {
    const user = auth.user as any;
    const [step, setStep] = useState(1);
    const [uploadsDone, setUploadsDone] = useState({
        id_front: !!(user?.id_front_path),
        id_back: !!(user?.id_back_path),
        selfie: !!(user?.selfie_path),
    });

    const basicForm = useForm({
        bio: user?.bio || '',
        expertise: user?.expertise || '',
        phone: user?.phone || '',
    });

    const canProceedToStep2 = !!user.bio && !!user.expertise && !!user.phone;

    const submitBasicInfo = (e: React.FormEvent) => {
        e.preventDefault();
        basicForm.post('/instructor/apply/basic-info', {
            onSuccess: () => setStep(2),
        });
    };

    const submitApplication = () => {
        router.post('/instructor/apply/submit', {}, {
            onSuccess: () => {},
        });
    };

    // --- Pending state ---
    if (status === 'pending') {
        return (
            <AppLayout>
                <Head title="طلب المدرب - قيد المراجعة" />
                <div className="min-h-[80vh] flex items-center justify-center py-12" dir="rtl">
                    <div className="max-w-md w-full mx-auto px-4 text-center">
                        <div className="w-24 h-24 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg className="w-12 h-12 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h1 className="text-2xl font-black text-slate-900 dark:text-white mb-3">طلبك قيد المراجعة</h1>
                        <p className="text-slate-500 dark:text-slate-400 mb-8">
                            تم استلام طلبك بنجاح. سيقوم فريقنا بمراجعة وثائقك وإشعارك بالنتيجة خلال 1-3 أيام عمل.
                        </p>
                        <div className="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-4 text-sm text-amber-700 dark:text-amber-400">
                            يمكنك الاستمرار في تصفح الدورات كطالب في انتظار القبول.
                        </div>
                    </div>
                </div>
            </AppLayout>
        );
    }

    // --- Rejected state ---
    if (status === 'rejected') {
        return (
            <AppLayout>
                <Head title="طلب المدرب - مرفوض" />
                <div className="min-h-[80vh] flex items-center justify-center py-12" dir="rtl">
                    <div className="max-w-lg w-full mx-auto px-4">

                        {/* Rejection Icon & Title */}
                        <div className="text-center mb-8">
                            <div className="w-24 h-24 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg className="w-12 h-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h1 className="text-2xl font-black text-slate-900 dark:text-white mb-2">تم رفض طلبك</h1>
                            <p className="text-slate-500 dark:text-slate-400 text-sm">
                                راجع سبب الرفض أدناه، وأعد تقديم الطلب بعد تصحيح المشكلة.
                            </p>
                        </div>

                        {/* Rejection Reason Box */}
                        {rejection_reason && (
                            <div className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-2xl p-6 mb-6">
                                <div className="flex items-start gap-3">
                                    <div className="w-8 h-8 bg-red-100 dark:bg-red-800/50 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                                        <svg className="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p className="text-sm font-bold text-red-700 dark:text-red-400 mb-2">سبب الرفض من الإدارة:</p>
                                        <p className="text-sm text-red-600 dark:text-red-300 leading-relaxed">{rejection_reason}</p>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Info box */}
                        <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-2xl p-4 mb-6">
                            <p className="text-sm text-blue-700 dark:text-blue-300">
                                <span className="font-bold">ملاحظة:</span> عند الضغط على "إعادة التقديم"، ستتمكن من تعديل جميع بياناتك ورفع الوثائق المطلوبة من جديد قبل إرسال الطلب.
                            </p>
                        </div>

                        {/* Reapply Button */}
                        <button
                            onClick={() => router.post('/instructor/apply/reapply')}
                            className="w-full py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition-colors shadow-lg shadow-indigo-600/25 flex items-center justify-center gap-2"
                        >
                            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            إعادة التقديم وتعديل البيانات
                        </button>

                    </div>
                </div>
            </AppLayout>
        );
    }

    // --- Application Form ---
    return (
        <AppLayout>
            <Head title="التقديم كمدرب" />
            <div className="min-h-screen py-12" dir="rtl">
                <div className="max-w-2xl mx-auto px-4">
                    {/* Header */}
                    <div className="text-center mb-10">
                        <div className="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg className="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h1 className="text-2xl font-black text-slate-900 dark:text-white">التقديم كمدرب</h1>
                        <p className="text-slate-500 mt-2">أكمل الخطوات التالية للتحقق من هويتك والبدء بتدريس الدورات</p>
                    </div>

                    <StepIndicator current={step} />

                    <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-3xl p-8 shadow-sm">

                        {/* Step 1: Basic Info */}
                        {step === 1 && (
                            <form onSubmit={submitBasicInfo} className="space-y-5">
                                <div>
                                    <h2 className="text-lg font-bold text-slate-900 dark:text-white mb-1">البيانات الأساسية</h2>
                                    <p className="text-sm text-slate-500">أخبرنا عن نفسك ومجال خبرتك</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">نبذة عنك <span className="text-red-400">*</span></label>
                                    <textarea
                                        value={basicForm.data.bio}
                                        onChange={e => basicForm.setData('bio', e.target.value)}
                                        rows={4}
                                        placeholder="اكتب نبذة مختصرة عن خبراتك ومسيرتك المهنية... (50 حرف على الأقل)"
                                        className="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
                                    />
                                    {basicForm.errors.bio && <p className="text-red-400 text-xs mt-1">{basicForm.errors.bio}</p>}
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">مجال الخبرة <span className="text-red-400">*</span></label>
                                    <input
                                        type="text"
                                        value={basicForm.data.expertise}
                                        onChange={e => basicForm.setData('expertise', e.target.value)}
                                        placeholder="مثال: تطوير الويب، التصميم الجرافيكي، التسويق الرقمي"
                                        className="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    />
                                    {basicForm.errors.expertise && <p className="text-red-400 text-xs mt-1">{basicForm.errors.expertise}</p>}
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">رقم الجوال <span className="text-red-400">*</span></label>
                                    <input
                                        type="tel"
                                        value={basicForm.data.phone}
                                        onChange={e => basicForm.setData('phone', e.target.value)}
                                        placeholder="+966 5X XXX XXXX"
                                        className="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    />
                                    {basicForm.errors.phone && <p className="text-red-400 text-xs mt-1">{basicForm.errors.phone}</p>}
                                </div>
                                <button type="submit" disabled={basicForm.processing}
                                    className="w-full py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition-colors disabled:opacity-50">
                                    {basicForm.processing ? 'جاري الحفظ...' : 'التالي: رفع وجه البطاقة'}
                                </button>
                            </form>
                        )}

                        {/* Step 2: ID Front */}
                        {step === 2 && (
                            <div className="space-y-6">
                                <div>
                                    <h2 className="text-lg font-bold text-slate-900 dark:text-white mb-1">وجه البطاقة الأمامي</h2>
                                    <p className="text-sm text-slate-500">ارفع صورة واضحة للوجه الأمامي لبطاقة هويتك الوطنية أو جواز سفرك</p>
                                </div>
                                <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-3 flex items-start gap-2">
                                    <svg className="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p className="text-xs text-blue-700 dark:text-blue-400">تأكد أن الصورة واضحة وغير مقصوصة وتظهر فيها جميع البيانات بوضوح.</p>
                                </div>
                                <ImageUploadBox label="صورة الوجه الأمامي" desc="JPG أو PNG، جودة عالية" fieldName="id_front" currentPath={user.id_front_path} onUploaded={() => setUploadsDone(p => ({ ...p, id_front: true }))} />
                                <div className="flex gap-3">
                                    <button onClick={() => setStep(1)} className="flex-1 py-3 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">السابق</button>
                                    <button onClick={() => setStep(3)} disabled={!uploadsDone.id_front}
                                        className="flex-1 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                        التالي: ظهر البطاقة
                                    </button>
                                </div>
                            </div>
                        )}

                        {/* Step 3: ID Back */}
                        {step === 3 && (
                            <div className="space-y-6">
                                <div>
                                    <h2 className="text-lg font-bold text-slate-900 dark:text-white mb-1">وجه البطاقة الخلفي</h2>
                                    <p className="text-sm text-slate-500">ارفع صورة واضحة للوجه الخلفي لبطاقة هويتك</p>
                                </div>
                                <ImageUploadBox label="صورة الوجه الخلفي" desc="JPG أو PNG، جودة عالية" fieldName="id_back" currentPath={user.id_back_path} onUploaded={() => setUploadsDone(p => ({ ...p, id_back: true }))} />
                                <div className="flex gap-3">
                                    <button onClick={() => setStep(2)} className="flex-1 py-3 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">السابق</button>
                                    <button onClick={() => setStep(4)} disabled={!uploadsDone.id_back}
                                        className="flex-1 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                        التالي: صورة السيلفي
                                    </button>
                                </div>
                            </div>
                        )}

                        {/* Step 4: Selfie */}
                        {step === 4 && (
                            <div className="space-y-6">
                                <div>
                                    <h2 className="text-lg font-bold text-slate-900 dark:text-white mb-1">صورة السيلفي مع البطاقة</h2>
                                    <p className="text-sm text-slate-500">التقط صورة سيلفي وأنت تمسك بطاقة هويتك بجوار وجهك</p>
                                </div>
                                <div className="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3 flex items-start gap-2">
                                    <svg className="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p className="text-xs text-amber-700 dark:text-amber-400">يجب أن يظهر وجهك ووجه البطاقة بوضوح في نفس الصورة.</p>
                                </div>
                                <ImageUploadBox label="سيلفي مع البطاقة" desc="JPG أو PNG، جودة عالية" fieldName="selfie" currentPath={user.selfie_path} onUploaded={() => setUploadsDone(p => ({ ...p, selfie: true }))} />
                                <div className="flex gap-3">
                                    <button onClick={() => setStep(3)} className="flex-1 py-3 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">السابق</button>
                                    <button onClick={() => setStep(5)} disabled={!uploadsDone.selfie}
                                        className="flex-1 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                        التالي: مراجعة وتقديم
                                    </button>
                                </div>
                            </div>
                        )}

                        {/* Step 5: Review & Submit */}
                        {step === 5 && (
                            <div className="space-y-6">
                                <div>
                                    <h2 className="text-lg font-bold text-slate-900 dark:text-white mb-1">مراجعة وتقديم الطلب</h2>
                                    <p className="text-sm text-slate-500">تأكد من صحة بياناتك قبل تقديم الطلب</p>
                                </div>

                                <div className="space-y-3">
                                    {[
                                        { label: 'الاسم', value: user.name },
                                        { label: 'البريد الإلكتروني', value: auth.user?.email },
                                        { label: 'مجال الخبرة', value: user.expertise },
                                        { label: 'رقم الجوال', value: user.phone },
                                    ].map(item => (
                                        <div key={item.label} className="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-700">
                                            <span className="text-sm text-slate-500">{item.label}</span>
                                            <span className="text-sm font-semibold text-slate-900 dark:text-white">{item.value || '—'}</span>
                                        </div>
                                    ))}
                                </div>

                                <div className="grid grid-cols-3 gap-3">
                                    {[
                                        { label: 'وجه البطاقة', done: uploadsDone.id_front },
                                        { label: 'ظهر البطاقة', done: uploadsDone.id_back },
                                        { label: 'صورة السيلفي', done: uploadsDone.selfie },
                                    ].map(doc => (
                                        <div key={doc.label} className={`p-3 rounded-xl border text-center ${doc.done ? 'border-emerald-300 bg-emerald-50 dark:bg-emerald-900/20' : 'border-red-300 bg-red-50 dark:bg-red-900/20'}`}>
                                            <svg className={`w-6 h-6 mx-auto mb-1 ${doc.done ? 'text-emerald-500' : 'text-red-400'}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                {doc.done ? <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" /> : <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />}
                                            </svg>
                                            <p className="text-xs font-semibold text-slate-700 dark:text-slate-300">{doc.label}</p>
                                        </div>
                                    ))}
                                </div>

                                <div className="bg-slate-50 dark:bg-slate-900 rounded-xl p-4 text-xs text-slate-500 leading-relaxed">
                                    بتقديم هذا الطلب، تؤكد أن جميع المعلومات المقدمة صحيحة ودقيقة، وتوافق على شروط الاستخدام وسياسة الخصوصية الخاصة بالمنصة.
                                </div>

                                <div className="flex gap-3">
                                    <button onClick={() => setStep(4)} className="flex-1 py-3 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">السابق</button>
                                    <button
                                        onClick={submitApplication}
                                        disabled={!uploadsDone.id_front || !uploadsDone.id_back || !uploadsDone.selfie}
                                        className="flex-1 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                    >
                                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        تقديم الطلب
                                    </button>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
