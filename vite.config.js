import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],

    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources'),
            
            // Alias yang benar untuk SNEAT (hindari styling aneh)
            '~sneat': path.resolve(__dirname, 'resources/css/sneat'),
        },
    },

    build: {
        // Biar font, png, svg, woff2, dll pindah ke:
        // public/build/assets/
        assetsDir: 'assets',
        
        // Optional tapi recommended untuk server shared hosting
        manifest: true,
    },
});
