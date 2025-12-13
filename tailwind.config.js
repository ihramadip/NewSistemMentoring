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
                body: ['Figtree', ...defaultTheme.fontFamily.sans],
                heading: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    sky: '#99eeff',
                    teal: '#1ca7a7',
                    gold: '#d4a017',
                    mist: '#f7f9fa',
                    ink: '#0f172a',
                },
            },
        },
    },

    plugins: [forms],
};
