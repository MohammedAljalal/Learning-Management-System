import { ButtonHTMLAttributes } from 'react';

interface Props extends ButtonHTMLAttributes<HTMLButtonElement> {
    variant?: 'primary' | 'secondary' | 'ghost' | 'danger';
    size?: 'sm' | 'md' | 'lg';
    loading?: boolean;
    children: React.ReactNode;
}

const variantClasses = {
    primary: 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/25 hover:-translate-y-0.5',
    secondary: 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700',
    ghost: 'bg-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800',
    danger: 'bg-red-600 hover:bg-red-500 text-white shadow-lg shadow-red-600/25 hover:-translate-y-0.5',
};

const sizeClasses = {
    sm: 'px-3 py-1.5 text-xs rounded-lg',
    md: 'px-4 py-2 text-sm rounded-xl',
    lg: 'px-6 py-3 text-base rounded-2xl',
};

export default function Button({ variant = 'primary', size = 'md', loading, children, className = '', disabled, ...props }: Props) {
    return (
        <button
            {...props}
            disabled={disabled || loading}
            className={`inline-flex items-center justify-center gap-2 font-bold transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 ${variantClasses[variant]} ${sizeClasses[size]} ${className}`}
        >
            {loading && (
                <svg className="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
            )}
            {children}
        </button>
    );
}
