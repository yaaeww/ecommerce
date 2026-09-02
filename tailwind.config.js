import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                display: ['Outfit', 'sans-serif'],
            },
            colors: {
                'brand-green': '#1B4D3E',
                'brand-green-dark': '#12352A',
                'brand-green-light': '#2D6A4F',
                'brand-amber': '#E88D14',
                'brand-amber-light': '#F3A638',
                'brand-cream': '#FAFAF7',
                'brand-slate': '#1E293B',
                indigo: {
                    500: '#2D6A4F',
                    600: '#1B4D3E',
                    700: '#12352A',
                },
                amber: {
                    500: '#d97706',
                },
                yellow: {
                    500: '#f59e0b',
                },
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
                slate: {
                    850: '#151f32',
                },
            },
        },
    },

    plugins: [forms],
};
