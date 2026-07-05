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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    DEFAULT: '#C06C3E',
                    50: '#f9f1ee',
                    100: '#f3e3dd',
                    200: '#e7c7bc',
                    300: '#dbaa9a',
                    400: '#cf8e79',
                    500: '#C06C3E',
                    600: '#ad6132',
                    700: '#90512a',
                    800: '#734121',
                    900: '#563119',
                },
                brown: {
                    50: '#fdf8f6',
                    100: '#f2e8e5',
                    200: '#eaddd7',
                    300: '#e0cec7',
                    400: '#d2bab0',
                    500: '#bfa094',
                    600: '#a18072',
                    700: '#977669',
                    800: '#846358',
                    900: '#6f4f44',
                    950: '#4b3a32',
                },
            },
        },
    },

    plugins: [forms],
};
