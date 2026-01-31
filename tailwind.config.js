import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // 重大度セレクターで使用する色
        'bg-green-500', 'bg-yellow-500', 'bg-orange-500', 'bg-red-500',
        'border-green-500', 'border-yellow-500', 'border-orange-500', 'border-red-500',
        'bg-green-50', 'bg-yellow-50', 'bg-orange-50', 'bg-red-50',
        'ring-green-500', 'ring-yellow-500', 'ring-orange-500', 'ring-red-500',
        'hover:border-green-300', 'hover:border-yellow-300', 'hover:border-orange-300', 'hover:border-red-300',
        'bg-green-100', 'bg-yellow-100', 'bg-orange-100', 'bg-red-100',
        'text-green-600', 'text-yellow-600', 'text-orange-600', 'text-red-600',
        'text-green-800', 'text-yellow-800', 'text-orange-800', 'text-red-800',
        'bg-green-100', 'bg-yellow-100', 'bg-orange-100', 'bg-red-100',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
