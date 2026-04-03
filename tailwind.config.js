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
            colors: {
                brand: {
                    navy:        '#1B3A6B',
                    navydark:    '#112549',
                    navylight:   '#E8EEF7',
                    orange:      '#F97316',
                    orangedark:  '#EA6C0A',
                    orangelight: '#FFF3E8',
                },
            },
            fontFamily: {
                display: ['Sora', ...defaultTheme.fontFamily.sans],
                body: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
