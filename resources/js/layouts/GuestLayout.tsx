import { Link } from '@inertiajs/react';

interface Props {
    children: React.ReactNode;
    title?: string;
    subtitle?: string;
}

export default function GuestLayout({ children, title, subtitle }: Props) {
    return (
        <div className="min-h-screen flex flex-col bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900">
            {/* Header */}
            <div className="flex justify-center pt-12 pb-8">
                <Link href="/" className="flex items-center gap-2.5">
                    <div className="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-xl shadow-indigo-500/40">
                        <svg className="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z" />
                        </svg>
                    </div>
                    <span className="text-xl font-black text-white">LMS</span>
                </Link>
            </div>

            {/* Card */}
            <div className="flex-1 flex items-start justify-center px-4 pb-12">
                <div className="w-full max-w-md">
                    {(title || subtitle) && (
                        <div className="text-center mb-8">
                            {title && <h1 className="text-2xl font-black text-white mb-2">{title}</h1>}
                            {subtitle && <p className="text-slate-400 text-sm">{subtitle}</p>}
                        </div>
                    )}
                    <div className="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl shadow-black/40">
                        {children}
                    </div>
                </div>
            </div>
        </div>
    );
}
