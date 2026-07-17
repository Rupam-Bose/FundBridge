import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/dashboard.css',
                'resources/css/dashboard-header.css',
                'resources/css/dashboard-sidebar.css',
                'resources/css/dashboard-footer.css',
                'resources/css/admin.css',
                'resources/js/app.js',
                'resources/js/fundbridge.js',
                'resources/js/dashboard.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
