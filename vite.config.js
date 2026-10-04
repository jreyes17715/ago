import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
    ],
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                app:    'resources/css/app.css',
                js:     'resources/js/app.js',
                editor: 'resources/css/editor-style.css',
            },
        },
    },
});
