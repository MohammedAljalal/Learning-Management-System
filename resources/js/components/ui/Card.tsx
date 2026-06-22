interface Props {
    children: React.ReactNode;
    className?: string;
    glass?: boolean;
    hover?: boolean;
    padding?: 'sm' | 'md' | 'lg' | 'none';
}

const paddingClasses = { sm: 'p-4', md: 'p-6', lg: 'p-8', none: '' };

export default function Card({ children, className = '', glass = false, hover = false, padding = 'md' }: Props) {
    return (
        <div className={`
            rounded-2xl shadow-sm
            ${glass
                ? 'bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl'
                : 'bg-white dark:bg-slate-800'}
            border border-slate-200/60 dark:border-slate-700/60
            ${hover ? 'hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer' : ''}
            ${paddingClasses[padding]}
            ${className}
        `}>
            {children}
        </div>
    );
}
