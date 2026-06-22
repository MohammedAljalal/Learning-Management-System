import { InputHTMLAttributes, forwardRef } from 'react';

interface Props extends InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    error?: string;
    icon?: React.ReactNode;
}

const Input = forwardRef<HTMLInputElement, Props>(({ label, error, icon, className = '', id, ...props }, ref) => {
    return (
        <div className="w-full">
            {label && (
                <label htmlFor={id} className="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                    {label}
                </label>
            )}
            <div className="relative">
                {icon && (
                    <div className="absolute inset-y-0 end-3 flex items-center pointer-events-none text-slate-400">
                        {icon}
                    </div>
                )}
                <input
                    ref={ref}
                    id={id}
                    className={`w-full ${icon ? 'pe-10' : ''} px-4 py-2.5 rounded-xl border text-sm transition-all duration-200
                        bg-white dark:bg-slate-800
                        border-slate-200 dark:border-slate-700
                        text-slate-900 dark:text-slate-100
                        placeholder-slate-400 dark:placeholder-slate-500
                        focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                        ${error ? 'border-red-400 focus:ring-red-500' : ''}
                        ${className}`}
                    {...props}
                />
            </div>
            {error && <p className="mt-1.5 text-xs text-red-500 font-medium">{error}</p>}
        </div>
    );
});

Input.displayName = 'Input';
export default Input;
