import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/frontend.css',
                'resources/js/frontend.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
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
        
        // Memastikan manifest diletakkan langsung di public/build/manifest.json
        manifest: 'manifest.json',
        
        // --- MATIKAN SOURCEMAP AGAR GHAIB TOTAL ---
        sourcemap: false,

        // --- DEEP CLEANING (Hapus Jejak Webpack) ---
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: false, // Biar console log kita tetep ada buat debug ringan
                drop_debugger: true,
            },
            format: {
                comments: false, // HAPUS SEMUA KOMENTAR (Termasuk jejak webpack)
            },
        },

        rollupOptions: {
            output: {
                manualChunks: undefined
            }
        }
    },
});
