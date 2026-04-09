import { defineConfig, loadEnv } from 'vite'; // Tambahkan loadEnv di sini
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import path from 'path';

export default defineConfig(({ mode }) => {
    // 1. Tarik variabel dari file .env
    const env = loadEnv(mode, process.cwd(), '');

    return {
        // 2. Tentukan base URL untuk asset hasil build
        // Jika VITE_ASSET_URL ada di .env, gunakan itu.
        // Akhiri dengan slash (/) agar path asset tidak berantakan.
        base: env.VITE_ASSET_URL ? `${env.VITE_ASSET_URL}/build/` : '/',

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
                '~sneat': path.resolve(__dirname, 'resources/css/sneat'),
            },
        },

        build: {
            assetsDir: 'assets',
            manifest: true,
        },
    };
});
