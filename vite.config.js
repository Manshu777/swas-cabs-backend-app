import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';


export default defineConfig({
    plugins: [
        laravel({
            input: [
                './resources/views/**/*.blade.php', // Blade templates
    './resources/js/**/*.js',           // JavaScript files
    './resources/js/**/*.vue',          // Vue files (if using Vue)
            ],
            refresh: true,
        }),
      tailwindcss(),
    ],
});
