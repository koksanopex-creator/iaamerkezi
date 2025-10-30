import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
        './app/Http/Livewire/**/*.php',
    ],

    // ================== KESİN ÇÖZÜM İÇİN EKLENEN BÖLÜM ==================
    safelist: [
        {
            pattern: /bg-(red|yellow|green|blue|gray|indigo|purple|pink|cyan)-(100)/,
        },
        {
            pattern: /text-(red|yellow|green|blue|gray|indigo|purple|pink|cyan)-(800)/,
        },
    ],
    // =================================================================

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};