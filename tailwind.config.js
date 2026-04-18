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
                    blue:        '#1e90ff',
                    bluedark:    '#1874cd',
                    bluelight:   '#e8f4ff',
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
