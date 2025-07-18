import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/dashboard.css',
                'resources/js/app.js',
                'resources/js/dashboard.js',
                'resources/js/dashboard-modern.js',
            ],
            refresh: true,
        }),
    ],
});
