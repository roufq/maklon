import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // Keep minimal input so Vite isn't building unused CSS
            input: ['resources/js/app.js'],
            refresh: true,
        }),
    ],
});
