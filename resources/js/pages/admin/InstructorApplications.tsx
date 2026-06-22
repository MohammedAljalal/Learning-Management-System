import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/AppLayout';
import type { PageProps } from '@/types';

interface Application {
    id: number;
    name: string;
    email: string;
    bio: string;
    expertise: string;
    phone: string;
    instructor_status: string;
    rejection_reason: string | null;
    id_front_url: string | null;
    id_back_url: string | null;
    selfie_url: string | null;
    created_at: string;
}

interface Props extends PageProps {
    applications: Application[];
    currentStatus: string;
    counts: { pending: number; approved: number; rejected: number };
}

function ImageModal({ src, onClose }: { src: string; onClose: () => void }) {
    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/80" onClick={onClose}>
            <div className="relative max-w-2xl max-h-[90vh]" onClick={e => e.stopPropagation()}>
                <button onClick={onClose} className="absolute -top-10 left-0 text-white hover:text-slate-300">
                    <svg className="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img src={src} alt="Document" className="rounded-xl max-h-[85vh] object-contain shadow-2xl" />
            </div>
        </div>
    );
}

function ApplicationCard({ app }: { app: Application }) {
    const [expanded, setExpanded] = useState(false);
    const [lightbox, setLightbox] = useState<string | null>(null);
    const [showReject, setShowReject] = useState(false);

    const rejectForm = useForm({ reason: '' });

    const approve = () => {
        router.post(`/admin/instructor-applications/${app.id}/approve`);
    };

    const reject = (e: React.FormEvent) => {
        e.preventDefault();
        rejectForm.post(`/admin/instructor-applications/${app.id}/reject`, {
            onSuccess: () => setShowReject(false),
        });
    };

    const statusConfig = {
        pending:  { label: 'قيد المراجعة', cls: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' },
        approved: { label: 'مقبول', cls: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
        rejected: { label: 'مرفوض', cls: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' },
    };
    const s = statusConfig[app.instructor_status as keyof typeof statusConfig];

    return (
        <div className="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow-sm">
            {lightbox && <ImageModal src={lightbox} onClose={() => setLightbox(null)} />}

            {/* Header */}
            <div className="flex items-center justify-between p-5 cursor-pointer" onClick={() => setExpanded(e => !e)}>
                <div className="flex items-center gap-4">
                    <div className="w-11 h-11 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 text-lg">
                        {app.name.charAt(0)}
                    </div>
                    <div>
                        <p className="font-bold text-slate-900 dark:text-white">{app.name}</p>
                        <p className="text-sm text-slate-500">{app.email}</p>
                    </div>
                </div>
                <div className="flex items-center gap-3">
                    <span className={`px-3 py-1 rounded-full text-xs font-bold ${s?.cls}`}>{s?.label}</span>
                    <svg className={`w-5 h-5 text-slate-400 transition-transform ${expanded ? 'rotate-180' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            {/* Expanded Detail */}
            {expanded && (
                <div className="px-5 pb-5 border-t border-slate-100 dark:border-slate-700 pt-5 space-y-5" dir="rtl">
                    {/* Info */}
                    <div className="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p className="text-slate-400 mb-0.5">مجال الخبرة</p>
                            <p className="font-semibold text-slate-800 dark:text-white">{app.expertise || '—'}</p>
                        </div>
                        <div>
                            <p className="text-slate-400 mb-0.5">رقم الجوال</p>
                            <p className="font-semibold text-slate-800 dark:text-white">{app.phone || '—'}</p>
                        </div>
                        <div className="col-span-2">
                            <p className="text-slate-400 mb-0.5">نبذة شخصية</p>
                            <p className="text-slate-700 dark:text-slate-300">{app.bio || '—'}</p>
                        </div>
                    </div>

                    {/* Documents */}
                    <div>
                        <p className="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">وثائق التحقق</p>
                        <div className="grid grid-cols-3 gap-3">
                            {[
                                { label: 'وجه البطاقة', url: app.id_front_url },
                                { label: 'ظهر البطاقة', url: app.id_back_url },
                                { label: 'سيلفي مع البطاقة', url: app.selfie_url },
                            ].map(doc => (
                                <div key={doc.label}>
                                    <p className="text-xs text-slate-400 mb-1.5">{doc.label}</p>
                                    {doc.url ? (
                                        <img
                                            src={doc.url}
                                            alt={doc.label}
                                            onClick={() => setLightbox(doc.url!)}
                                            className="w-full h-28 object-cover rounded-xl cursor-pointer hover:opacity-80 transition-opacity border border-slate-200 dark:border-slate-700"
                                        />
                                    ) : (
                                        <div className="w-full h-28 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 flex items-center justify-center">
                                            <p className="text-xs text-slate-400">لم يُرفع</p>
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Rejection reason if rejected */}
                    {app.instructor_status === 'rejected' && app.rejection_reason && (
                        <div className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3">
                            <p className="text-xs font-semibold text-red-600 dark:text-red-400 mb-1">سبب الرفض:</p>
                            <p className="text-sm text-red-700 dark:text-red-400">{app.rejection_reason}</p>
                        </div>
                    )}

                    {/* Actions */}
                    {app.instructor_status === 'pending' && (
                        <div className="flex gap-3 pt-2">
                            <button onClick={approve}
                                className="flex-1 flex items-center justify-center gap-2 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition-colors text-sm">
                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                                </svg>
                                قبول الطلب
                            </button>
                            <button onClick={() => setShowReject(v => !v)}
                                className="flex-1 flex items-center justify-center gap-2 py-2.5 bg-red-600 hover:bg-red-500 text-white font-bold rounded-xl transition-colors text-sm">
                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                رفض الطلب
                            </button>
                        </div>
                    )}

                    {/* Reject Form */}
                    {showReject && (
                        <form onSubmit={reject} className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 space-y-3">
                            <p className="text-sm font-semibold text-red-700 dark:text-red-400">سبب الرفض</p>
                            <textarea
                                value={rejectForm.data.reason}
                                onChange={e => rejectForm.setData('reason', e.target.value)}
                                rows={3}
                                placeholder="اكتب سبب رفض الطلب ليظهر للمدرب..."
                                className="w-full px-3 py-2 rounded-lg border border-red-300 dark:border-red-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"
                            />
                            {rejectForm.errors.reason && <p className="text-red-500 text-xs">{rejectForm.errors.reason}</p>}
                            <div className="flex gap-2">
                                <button type="button" onClick={() => setShowReject(false)} className="flex-1 py-2 border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 rounded-lg text-sm font-semibold">إلغاء</button>
                                <button type="submit" disabled={rejectForm.processing} className="flex-1 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg text-sm font-bold disabled:opacity-50">
                                    {rejectForm.processing ? 'جاري الرفض...' : 'تأكيد الرفض'}
                                </button>
                            </div>
                        </form>
                    )}
                </div>
            )}
        </div>
    );
}

export default function InstructorApplications({ applications, currentStatus, counts }: Props) {
    const tabs = [
        { key: 'pending', label: 'قيد المراجعة', count: counts.pending, color: 'amber' },
        { key: 'approved', label: 'مقبولون', count: counts.approved, color: 'emerald' },
        { key: 'rejected', label: 'مرفوضون', count: counts.rejected, color: 'red' },
        { key: 'all', label: 'الكل', count: counts.pending + counts.approved + counts.rejected, color: 'indigo' },
    ];

    return (
        <AppLayout>
            <Head title="طلبات المدربين" />
            <div className="py-10 min-h-screen" dir="rtl">
                <div className="max-w-4xl mx-auto px-4">
                    {/* Header */}
                    <div className="mb-8">
                        <h1 className="text-2xl font-black text-slate-900 dark:text-white">طلبات التحقق من المدربين</h1>
                        <p className="text-slate-500 mt-1">راجع وثائق المدربين المتقدمين واتخذ قراراً بالقبول أو الرفض</p>
                    </div>

                    {/* Tabs */}
                    <div className="flex gap-2 mb-6 flex-wrap">
                        {tabs.map(tab => (
                            <button
                                key={tab.key}
                                onClick={() => router.get('/admin/instructor-applications', { status: tab.key })}
                                className={`flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-colors ${
                                    currentStatus === tab.key
                                        ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20'
                                        : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700'
                                }`}
                            >
                                {tab.label}
                                <span className={`px-2 py-0.5 rounded-full text-xs font-bold ${currentStatus === tab.key ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400'}`}>
                                    {tab.count}
                                </span>
                            </button>
                        ))}
                    </div>

                    {/* Applications */}
                    <div className="space-y-4">
                        {applications.length === 0 ? (
                            <div className="text-center py-16 text-slate-400">
                                <svg className="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p className="font-semibold">لا توجد طلبات في هذه الفئة</p>
                            </div>
                        ) : (
                            applications.map(app => <ApplicationCard key={app.id} app={app} />)
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
