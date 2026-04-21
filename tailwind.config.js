import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        container: {
            center: true,
            padding: {
                DEFAULT: '1rem',
                sm: '1.5rem',
                lg: '2rem',
            },
            screens: {
                '2xl': '1280px',
            },
        },
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            letterSpacing: {
                tightest: '-0.04em',
            },
            colors: {
                brand: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                },
            },
            boxShadow: {
                soft: '0 1px 2px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.06)',
                lift: '0 10px 30px -10px rgba(15, 23, 42, 0.12), 0 4px 8px -4px rgba(15, 23, 42, 0.08)',
                glow: '0 0 0 1px rgba(99, 102, 241, 0.2), 0 8px 24px -6px rgba(99, 102, 241, 0.25)',
            },
            backgroundImage: {
                'grid-slate':
                    "linear-gradient(rgba(15,23,42,0.04) 1px, transparent 1px), linear-gradient(to right, rgba(15,23,42,0.04) 1px, transparent 1px)",
                'brand-gradient':
                    'linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%)',
            },
            keyframes: {
                'fade-in': {
                    '0%': { opacity: '0', transform: 'translateY(4px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-400px 0' },
                    '100%': { backgroundPosition: '400px 0' },
                },
            },
            animation: {
                'fade-in': 'fade-in 0.25s ease-out',
                shimmer: 'shimmer 1.4s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
