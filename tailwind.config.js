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
             'gray': { DEFAULT: '#272728', 100: '#080808', 200: '#0f0f0f', 300: '#171717', 400: '#1e1e1f', 500: '#272728', 600: '#515152', 700: '#7c7c7e', 800: '#a7a7a9', 900: '#d3d3d4' },
             'gray2': { DEFAULT: '#7f8b8d', 100: '#191c1d', 200: '#333839', 300: '#4c5456', 400: '#667173', 500: '#7f8b8d', 600: '#99a3a5', 700: '#b3babb', 800: '#ccd1d2', 900: '#e6e8e8' },
             'azure': { DEFAULT: '#51dde7', 100: '#073437', 200: '#0f676e', 300: '#169ba4', 400: '#1ecedb', 500: '#51dde7', 600: '#73e4ec', 700: '#96ebf1', 800: '#b9f1f5', 900: '#dcf8fa' },
             'blue': { DEFAULT: '#004759', 100: '#000e11', 200: '#001c23', 300: '#002a34', 400: '#003745', 500: '#004759', 600: '#0089ab', 700: '#01ccff', 800: '#56ddff', 900: '#aaeeff' } }
        },
    },
    plugins: [forms],
};
