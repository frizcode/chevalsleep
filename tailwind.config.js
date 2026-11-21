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
                    950: '#050914',
                    900: '#080E1E', // Primary Dark Canvas (Midnight Starry Cape)
                    850: '#0B1327',
                    800: '#0F182E', // Dark Card/Panel Surface
                    750: '#14203D', // Elevated Surface
                    700: '#1E2D4A', // Subtle Starry Navy Border
                    600: '#2A3F66',
                    500: '#3D598F',
                },
                sail: {
                    50: '#F4F7FB',  // Primary Light Canvas (Admiral Jacket White / Naval Porcelain)
                    100: '#EAF0F8', // Light surface elevated
                    200: '#D5E1F0', // Light border
                    300: '#BDCEE4',
                    400: '#94A8C4',
                },
                brass: {
                    300: '#F5E6BA',
                    400: '#EAB308', // Anchor Brass
                    500: '#D4AF37', // Clasp Brass Gold
                    600: '#B5942B',
                    700: '#8C6C22',
                },
                cerulean: {
                    300: '#7DD3FC', // Collar Cyan Light
                    400: '#38BDF8', // Ice Sky Cyan
                    500: '#0EA5E9',
                    600: '#0284C7', // Cerulean Sea
                    700: '#0369A1',
                    800: '#075985',
                },
                charcoal: {
                    900: '#0B0F19',
                    800: '#111827', // Charcoal Shorts Dark Header
                    700: '#1E293B',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
        },
    },

    plugins: [forms],
};
