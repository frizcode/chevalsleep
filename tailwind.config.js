import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                navy: {
                    950: '#030B17',
                    900: '#090E1A',
                    850: '#0E172B',
                    800: '#11192E',
                    700: '#1B2A4A',
                    600: '#273C66',
                },
                starlight: {
                    300: '#FDE68A',
                    400: '#FBBF24',
                    500: '#F59E0B',
                    600: '#D97706',
                },
                marine: {
                    400: '#38BDF8',
                    500: '#0EA5E9',
                    600: '#0284C7',
                    700: '#0369A1',
                }
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
