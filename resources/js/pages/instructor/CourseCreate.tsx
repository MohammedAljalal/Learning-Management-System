import { Head, Link, useForm } from '@inertiajs/react';
import { useState, useRef } from 'react';
import AppLayout from '@/layouts/AppLayout';

interface Category {
    id: number;
    name: string;
}

interface Props {
    categories: Category[];
    flash?: { success?: string; error?: string };
}

export default function CourseCreate({ categories, flash }: Props) {
    const { data, setData, post, processing, errors, clearErrors } = useForm({
        title: '',
        category_id: '',
        price: '',
        difficulty: 'beginner',
        description: '',
        cover_image: null as File | null,
    });

    const [preview, setPreview] = useState<string | null>(null);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            setData('cover_image', file);
            setPreview(URL.createObjectURL(file));
            clearErrors('cover_image');
        }
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/instructor/courses', {
            forceFormData: true,
        });
    };

    const difficultyOptions = [
        { value: 'beginner', label: 'مبتدئ', desc: 'مناسب للمبتدئين', color: 'text-emerald-600' },
        { value: 'intermediate', label: 'متوسط', desc: 'يتطلب معرفة أساسية', color: 'text-amber-600' },
        { value: 'expert', label: 'متقدم', desc: 'للمحترفين', color: 'text-red-600' },
    ];

    return (
        <AppLayout>
            <Head title="إنشاء دورة جديدة" />
            
            <div className="py-8 min-h-screen bg-slate-50 dark:bg-slate-900" dir="rtl">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    
                    {/* Header */}
                    <div className="mb-8 flex items-center gap-4">
                        <Link href="/instructor/courses" className="text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                            <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                        <div>
                            <h1 className="text-2xl font-black text-slate-900 dark:text-white">إنشاء دورة جديدة</h1>
                            <p className="text-slate-500 dark:text-slate-400 text-sm mt-1">أضف التفاصيل الأساسية لدورتك للبدء</p>
                        </div>
                    </div>

                    {/* Flash Error */}
                    {flash?.error && (
                        <div className="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-400 text-sm">
                            {flash.error}
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-6">
                        
                        {/* Main Info */}
                        <div className="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 md:p-8">
                            <h2 className="text-lg font-bold text-slate-800 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-4 flex items-center gap-2">
                                <svg className="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                المعلومات الأساسية
                            </h2>

                            {/* Title */}
                            <div className="mb-5">
                                <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    عنوان الدورة <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.title}
                                    onChange={e => { setData('title', e.target.value); clearErrors('title'); }}
                                    placeholder="مثال: دورة تطوير تطبيقات الويب الشاملة بـ React"
                                    className={`w-full rounded-xl border ${errors.title ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700'} text-slate-900 dark:text-white shadow-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors`}
                                    required
                                />
                                {errors.title && <p className="mt-1.5 text-sm text-red-500">{errors.title}</p>}
                            </div>

                            {/* Category + Difficulty */}
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        التصنيف <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        value={data.category_id}
                                        onChange={e => { setData('category_id', e.target.value); clearErrors('category_id'); }}
                                        className={`w-full rounded-xl border ${errors.category_id ? 'border-red-400' : 'border-slate-300 dark:border-slate-600'} bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500`}
                                        required
                                    >
                                        <option value="">اختر التصنيف</option>
                                        {categories.map(cat => (
                                            <option key={cat.id} value={cat.id}>{cat.name}</option>
                                        ))}
                                    </select>
                                    {errors.category_id && <p className="mt-1.5 text-sm text-red-500">{errors.category_id}</p>}
                                </div>

                                <div>
                                    <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                        السعر (بالدولار) <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <span className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">$</span>
                                        <input
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            value={data.price}
                                            onChange={e => { setData('price', e.target.value); clearErrors('price'); }}
                                            placeholder="0.00"
                                            className={`w-full rounded-xl border ${errors.price ? 'border-red-400' : 'border-slate-300 dark:border-slate-600'} bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm pl-4 pr-8 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500`}
                                            required
                                        />
                                    </div>
                                    <p className="mt-1 text-xs text-slate-400">اكتب 0 للدورات المجانية</p>
                                    {errors.price && <p className="mt-1.5 text-sm text-red-500">{errors.price}</p>}
                                </div>
                            </div>

                            {/* Difficulty */}
                            <div className="mb-5">
                                <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                                    مستوى الدورة <span className="text-red-500">*</span>
                                </label>
                                <div className="grid grid-cols-3 gap-3">
                                    {difficultyOptions.map(opt => (
                                        <button
                                            key={opt.value}
                                            type="button"
                                            onClick={() => setData('difficulty', opt.value as any)}
                                            className={`p-3 rounded-xl border-2 text-right transition-all ${data.difficulty === opt.value ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300'}`}
                                        >
                                            <div className={`text-sm font-bold ${data.difficulty === opt.value ? 'text-indigo-700 dark:text-indigo-300' : 'text-slate-700 dark:text-slate-300'}`}>{opt.label}</div>
                                            <div className="text-xs text-slate-400 mt-0.5">{opt.desc}</div>
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Description */}
                            <div>
                                <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    وصف الدورة <span className="text-red-500">*</span>
                                </label>
                                <textarea
                                    value={data.description}
                                    onChange={e => { setData('description', e.target.value); clearErrors('description'); }}
                                    rows={5}
                                    className={`w-full rounded-xl border ${errors.description ? 'border-red-400 bg-red-50 dark:bg-red-900/10' : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700'} text-slate-900 dark:text-white shadow-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 placeholder-slate-400`}
                                    placeholder="اكتب وصفاً مفصلاً عما سيتعلمه الطلاب في هذه الدورة..."
                                    required
                                />
                                <div className="flex justify-between mt-1">
                                    {errors.description ? (
                                        <p className="text-sm text-red-500">{errors.description}</p>
                                    ) : (
                                        <p className="text-xs text-slate-400">على الأقل 20 حرفاً</p>
                                    )}
                                    <span className={`text-xs ${data.description.length < 20 ? 'text-red-400' : 'text-emerald-500'}`}>
                                        {data.description.length} حرف
                                    </span>
                                </div>
                            </div>
                        </div>

                        {/* Cover Image */}
                        <div className="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 md:p-8">
                            <h2 className="text-lg font-bold text-slate-800 dark:text-white mb-6 border-b border-slate-100 dark:border-slate-700 pb-4 flex items-center gap-2">
                                <svg className="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                صورة الغلاف
                            </h2>

                            <div
                                onClick={() => fileInputRef.current?.click()}
                                className={`relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed cursor-pointer transition-colors overflow-hidden ${preview ? 'border-indigo-400' : 'border-slate-300 dark:border-slate-600 hover:border-indigo-400 dark:hover:border-indigo-500'}`}
                                style={{ minHeight: '220px' }}
                            >
                                {preview ? (
                                    <>
                                        <img src={preview} alt="Preview" className="w-full object-cover" style={{ maxHeight: '300px' }} />
                                        <div className="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                            <span className="text-white font-bold text-sm bg-black/50 px-4 py-2 rounded-lg">انقر لتغيير الصورة</span>
                                        </div>
                                    </>
                                ) : (
                                    <div className="p-10 text-center">
                                        <svg className="mx-auto h-14 w-14 text-slate-300 dark:text-slate-600 mb-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path fillRule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clipRule="evenodd" />
                                        </svg>
                                        <p className="text-slate-500 dark:text-slate-400 font-semibold text-sm mb-1">انقر لرفع صورة الغلاف</p>
                                        <p className="text-slate-400 dark:text-slate-500 text-xs">PNG, JPG, WEBP · حتى 5 ميجابايت · 1920×1080 مفضل</p>
                                    </div>
                                )}
                                <input
                                    ref={fileInputRef}
                                    type="file"
                                    className="sr-only"
                                    accept="image/png,image/jpeg,image/webp"
                                    onChange={handleImageChange}
                                />
                            </div>
                            {errors.cover_image && <p className="mt-2 text-sm text-red-500">{errors.cover_image}</p>}
                        </div>

                        {/* Submit */}
                        <div className="flex items-center justify-between pt-2">
                            <Link href="/instructor/courses" className="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                                إلغاء والعودة
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 disabled:bg-indigo-400 text-white font-bold px-8 py-3 rounded-xl transition-all shadow-lg shadow-indigo-600/25 disabled:cursor-not-allowed"
                            >
                                {processing ? (
                                    <>
                                        <svg className="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                        جاري الإنشاء...
                                    </>
                                ) : (
                                    <>
                                        إنشاء الدورة ومتابعة
                                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                        </svg>
                                    </>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
