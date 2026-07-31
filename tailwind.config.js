import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                brand: {
                    deep:  '#4A0E2E',
                    mid:   '#7B1D4C',
                    light: '#B76E79',
                    gold:  '#C9A84C',
                },
            },
            backdropBlur: {
                xs: '2px',
            },
            animation: {
                'fade-up': 'fadeInUp 0.4s ease forwards',
                'float':   'float 6s ease-in-out infinite',
            },
            keyframes: {
                fadeInUp: {
                    '0%':   { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                float: {
                    '0%,100%': { transform: 'translateY(0px)' },
                    '50%':     { transform: 'translateY(-12px)' },
                },
            },
        },
    },

    plugins: [forms],
};
