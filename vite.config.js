import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
<<<<<<< HEAD
            input: ["resources/css/app.css", "resources/js/app.js"],
=======
            // Daftarkan semua file aset yang Anda gunakan di sini
            input: [
                // File default (bisa Anda hapus jika tidak digunakan)
                'resources/css/app.css',
                'resources/js/app.js',

                // File-file dari tema Sneat yang dibutuhkan oleh halaman login
                'resources/sneat/assets/vendor/fonts/boxicons.css',
                'resources/sneat/assets/vendor/css/core.css',
                'resources/sneat/assets/vendor/css/theme-default.css',
                'resources/sneat/assets/css/demo.css',
                'resources/sneat/assets/vendor/css/pages/page-auth.css',
            ],
>>>>>>> origin/modul/kepegawaian
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "~sneat": path.resolve(__dirname, "resources/sneat/assets"),
        },
    },
});
