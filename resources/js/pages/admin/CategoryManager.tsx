import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';
import type { PageProps, Category } from '@/types';

interface Props extends PageProps {
    categories: Category[];
}

export default function CategoryManager({ categories }: Props) {
    const [adding, setAdding] = useState(false);
    const [name, setName] = useState('');

    const addCategory = (e: React.FormEvent) => {
        e.preventDefault();
        router.post('/admin/categories', { name }, {
            onSuccess: () => {
                setAdding(false);
                setName('');
            }
        });
    };

    const deleteCategory = (id: number) => {
        if (confirm('هل أنت متأكد من حذف هذا التصنيف؟')) {
            router.delete(`/admin/categories/${id}`);
        }
    };

    return (
        <AppLayout>
            <Head title="إدارة التصنيفات" />
            <div className="py-8 min-h-screen" dir="rtl">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between mb-8">
                        <div>
                            <h1 className="text-2xl font-black text-slate-900 dark:text-white">إدارة التصنيفات</h1>
                            <p className="text-slate-500 dark:text-slate-400 text-sm mt-1">{categories.length} تصنيف متاح</p>
                        </div>
                        <button
                            onClick={() => setAdding(true)}
                            className="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-600/25 transition-all"
                        >
                            إضافة تصنيف +
                        </button>
                    </div>

                    {adding && (
                        <form onSubmit={addCategory} className="mb-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm flex items-end gap-4">
                            <div className="flex-1">
                                <label className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">اسم التصنيف الجديد</label>
                                <input
                                    type="text"
                                    autoFocus
                                    value={name}
                                    onChange={e => setName(e.target.value)}
                                    className="w-full px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
                            <div className="flex gap-2">
                                <button type="button" onClick={() => setAdding(false)} className="px-4 py-2 text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl font-semibold">إلغاء</button>
                                <button type="submit" disabled={!name.trim()} className="px-6 py-2 text-sm bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold disabled:opacity-50 shadow-sm">حفظ</button>
                            </div>
                        </form>
                    )}

                    <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm overflow-hidden">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                                    <th className="text-right px-6 py-4 font-semibold">الرقم</th>
                                    <th className="text-right px-6 py-4 font-semibold">الاسم</th>
                                    <th className="text-right px-6 py-4 font-semibold">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-700">
                                {categories.map(cat => (
                                    <tr key={cat.id} className="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                        <td className="px-6 py-4 text-slate-500 dark:text-slate-400">#{cat.id}</td>
                                        <td className="px-6 py-4 font-bold text-slate-900 dark:text-white">{cat.name}</td>
                                        <td className="px-6 py-4">
                                            <button onClick={() => deleteCategory(cat.id)} className="text-red-500 hover:text-red-600 font-semibold hover:underline">
                                                حذف
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                                {categories.length === 0 && (
                                    <tr>
                                        <td colSpan={3} className="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                            لا توجد تصنيفات مضافة.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
