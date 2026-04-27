import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Livewire/**/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                pacis: {
                    50: '#eef6ff',
                    100: '#d9eaff',
                    200: '#bcdbff',
                    300: '#8ec4ff',
                    400: '#58a2ff',
                    500: '#2f7dff',
                    600: '#1a5fe6',
                    700: '#174bba',
                    800: '#183f93',
                    900: '#183874',
                },
            },
        },
    },
    plugins: [forms],
};
