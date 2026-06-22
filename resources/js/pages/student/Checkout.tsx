import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/AppLayout';
import type { PageProps, Course } from '@/types';

interface Props extends PageProps {
    course: Course;
}

export default function Checkout({ course, auth }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        nameOnCard: auth.user?.name ?? '',
        cardNumber: '',
        expiryDate: '',
        cvc: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(`/checkout/${course.slug}`);
    };

    return (
        <AppLayout>
            <Head title="إتمام الشراء" />
            <div className="py-12" dir="rtl">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    
                    <div className="mb-6 flex items-center justify-between">
                        <h1 className="text-3xl font-bold text-slate-900 dark:text-white">إتمام الشراء</h1>
                        <Link href={`/courses/${course.slug}`} className="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-medium flex items-center gap-1 transition-colors">
                            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                            </svg>
                            عودة للدورة
                        </Link>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        {/* Payment Form */}
                        <div className="md:col-span-2">
                            <div className="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                                <h2 className="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                                    <svg className="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    بيانات الدفع
                                </h2>

                                <form onSubmit={submit} className="space-y-6">
                                    
                                    {/* Name on Card */}
                                    <div>
                                        <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">الاسم على البطاقة</label>
                                        <input 
                                            type="text" 
                                            value={data.nameOnCard} 
                                            onChange={e => setData('nameOnCard', e.target.value)} 
                                            className="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm" 
                                            placeholder="John Doe" 
                                        />
                                        {errors.nameOnCard && <span className="text-red-500 text-xs mt-1 block">{errors.nameOnCard}</span>}
                                    </div>

                                    {/* Card Number */}
                                    <div>
                                        <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">رقم البطاقة (للتجربة استخدم 4242...)</label>
                                        <div className="relative">
                                            <input 
                                                type="text" 
                                                value={data.cardNumber} 
                                                onChange={e => setData('cardNumber', e.target.value)} 
                                                placeholder="0000 0000 0000 0000" 
                                                className="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm pl-10 text-left" 
                                                dir="ltr"
                                            />
                                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg className="h-5 w-5 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        {errors.cardNumber && <span className="text-red-500 text-xs mt-1 block">{errors.cardNumber}</span>}
                                    </div>

                                    <div className="grid grid-cols-2 gap-6">
                                        {/* Expiry */}
                                        <div>
                                            <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">تاريخ الانتهاء</label>
                                            <input 
                                                type="text" 
                                                value={data.expiryDate} 
                                                onChange={e => setData('expiryDate', e.target.value)} 
                                                placeholder="MM/YY" 
                                                className="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm text-center" 
                                                dir="ltr"
                                            />
                                            {errors.expiryDate && <span className="text-red-500 text-xs mt-1 block">{errors.expiryDate}</span>}
                                        </div>

                                        {/* CVC */}
                                        <div>
                                            <label className="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">الرمز السري (CVC)</label>
                                            <input 
                                                type="text" 
                                                value={data.cvc} 
                                                onChange={e => setData('cvc', e.target.value)} 
                                                placeholder="123" 
                                                className="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 transition-colors shadow-sm text-center" 
                                                dir="ltr"
                                            />
                                            {errors.cvc && <span className="text-red-500 text-xs mt-1 block">{errors.cvc}</span>}
                                        </div>
                                    </div>

                                    <div className="pt-4 border-t border-slate-100 dark:border-slate-700">
                                        <button 
                                            type="submit" 
                                            disabled={processing} 
                                            className="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition-colors shadow-lg shadow-indigo-600/30 disabled:opacity-70 disabled:cursor-not-allowed"
                                        >
                                            {processing ? (
                                                <span>جاري المعالجة...</span>
                                            ) : (
                                                <>
                                                    <span>ادفع الآن (${Number(course.price).toFixed(2)})</span>
                                                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </>
                                            )}
                                        </button>
                                    </div>
                                    
                                    <p className="text-xs text-center text-slate-500 mt-4 flex items-center justify-center gap-1">
                                        <svg className="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        عملية الدفع آمنة ومحاكاة 100%
                                    </p>
                                </form>
                            </div>
                        </div>

                        {/* Order Summary */}
                        <div className="md:col-span-1">
                            <div className="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 sticky top-24">
                                <h3 className="text-lg font-bold text-slate-900 dark:text-white mb-4">ملخص الطلب</h3>
                                
                                <div className="flex items-start gap-3 mb-6 pb-6 border-b border-slate-200 dark:border-slate-700">
                                    <div className="w-16 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900 overflow-hidden shrink-0">
                                        {course.thumbnail_url && (
                                            <img src={course.thumbnail_url} alt={course.title} className="w-full h-full object-cover" />
                                        )}
                                    </div>
                                    <div>
                                        <h4 className="text-sm font-semibold text-slate-900 dark:text-white line-clamp-2">{course.title}</h4>
                                        <p className="text-xs text-slate-500 mt-1">{course.instructor?.name}</p>
                                    </div>
                                </div>

                                <div className="space-y-3 text-sm">
                                    <div className="flex justify-between text-slate-600 dark:text-slate-400">
                                        <span>السعر الأصلي</span>
                                        <span>${Number(course.price).toFixed(2)}</span>
                                    </div>
                                    <div className="flex justify-between text-slate-600 dark:text-slate-400">
                                        <span>الخصم</span>
                                        <span className="text-emerald-500">-$0.00</span>
                                    </div>
                                    <div className="flex justify-between font-bold text-lg text-slate-900 dark:text-white pt-4 border-t border-slate-200 dark:border-slate-700 mt-4">
                                        <span>الإجمالي</span>
                                        <span>${Number(course.price).toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
