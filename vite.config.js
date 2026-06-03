import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pages/home.js',
                'resources/js/pages/auto-control.js',
                'resources/js/pages/manual-control.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
