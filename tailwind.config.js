import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/**/**/*.blade.php',
        './resources/js/**/*.{js,ts,jsx,tsx}',
        './app/**/*.php',
        './app/Livewire/**/*.php',
    ],

    safelist: [
        // Layout & spacing
        'min-h-screen', 'max-w-7xl', 'mx-auto', 'py-8', 'py-6', 'px-6', 'p-6', 'p-8',
        'sm:px-6', 'lg:px-8', 'space-y-8', 'space-y-6', 'space-y-4', 'space-y-3',
        // Grid
        'grid', 'grid-cols-1', 'lg:grid-cols-3', 'lg:col-span-2', 'gap-6', 'gap-4', 'gap-3', 'gap-2', 'gap-5',
        // Backgrounds
        'bg-gradient-to-l', 'bg-gradient-to-br', 'bg-gradient-to-r',
        'from-indigo-600', 'from-indigo-500', 'from-purple-600', 'from-amber-400',
        'to-purple-700', 'to-purple-600', 'to-indigo-700', 'to-amber-400',
        'bg-white', 'bg-white/80', 'bg-white/20', 'bg-white/10', 'bg-white/5',
        'dark:bg-slate-800/80', 'dark:bg-slate-700/40', 'dark:bg-slate-800',
        'bg-slate-50', 'bg-slate-100', 'bg-slate-200',
        'dark:bg-slate-700', 'dark:bg-slate-900',
        'bg-indigo-600', 'bg-indigo-500', 'bg-amber-400', 'bg-amber-500',
        'bg-emerald-50', 'bg-emerald-500',
        'dark:bg-amber-900/20', 'dark:bg-emerald-900/30', 'dark:bg-indigo-900/20',
        // Shadows
        'shadow-xl', 'shadow-sm', 'shadow-lg', 'shadow-indigo-600/20', 'shadow-indigo-500/30',
        // Blur
        'backdrop-blur-xl', 'backdrop-blur-sm',
        // Rounded
        'rounded-2xl', 'rounded-xl', 'rounded-full', 'rounded-lg', 'rounded',
        // Text colors
        'text-white', 'text-white/70', 'text-white/60', 'text-white/50',
        'text-slate-900', 'text-slate-700', 'text-slate-600', 'text-slate-500', 'text-slate-400',
        'dark:text-white', 'dark:text-slate-100', 'dark:text-slate-200', 'dark:text-slate-300', 'dark:text-slate-400',
        'text-indigo-600', 'dark:text-indigo-400',
        'text-emerald-600', 'dark:text-emerald-400',
        'text-amber-600', 'dark:text-amber-400',
        'text-red-600', 'dark:text-red-400',
        // Text sizes & weights
        'text-xs', 'text-sm', 'text-base', 'text-lg', 'text-xl', 'text-2xl', 'text-3xl', 'text-4xl',
        'font-black', 'font-bold', 'font-semibold', 'font-medium', 'font-normal',
        // Flex
        'flex', 'flex-col', 'flex-row', 'flex-1', 'items-center', 'items-start',
        'justify-between', 'justify-center', 'shrink-0', 'flex-wrap',
        'sm:flex-row', 'sm:w-72',
        // Width/Height
        'w-full', 'w-16', 'w-14', 'w-12', 'w-9', 'w-8', 'w-7', 'w-6', 'w-5', 'w-4', 'w-2',
        'h-16', 'h-12', 'h-3', 'h-1.5', 'h-full', 'h-9', 'h-8',
        // Borders
        'border', 'border-amber-200', 'dark:border-amber-800',
        'border-slate-100', 'dark:border-slate-700',
        'border-emerald-100', 'dark:border-emerald-800',
        // Overflow
        'overflow-hidden', 'overflow-x-hidden', 'min-w-0', 'truncate',
        // Transitions
        'transition-colors', 'transition-all', 'duration-700',
        // Hover
        'hover:bg-slate-50', 'dark:hover:bg-slate-700/40',
        'hover:text-indigo-600', 'dark:hover:text-indigo-400',
        'group', 'group-hover:text-indigo-600', 'dark:group-hover:text-indigo-400',
        // Opacity
        'opacity-100', 'opacity-60',
        // Position
        'relative', 'absolute', '-bottom-1', '-end-1',
        // Object
        'object-cover',
        // Padding
        'py-2.5', 'px-3', 'p-3', 'mt-1.5', 'mt-1', 'mb-5', 'mb-4', 'mb-1.5', 'ms-2',
        // Leading
        'leading-snug', 'leading-tight',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Cairo', 'IBM Plex Sans Arabic', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};

