import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import livewire from '@livewire/vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        livewire(),
    ],
});
