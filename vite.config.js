// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // Remove the css line if you don't have an app.css file
            input: ['resources/js/app.js'], 
            refresh: true,
        }),
    ],
});