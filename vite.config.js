import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import path from 'path'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js',
             "resources/css/app.css",
                "resources/js/app.js",

                "resources/sneat/assets/vendor/fonts/boxicons.css", // (Jika ini yang Anda perbaiki sebelumnya)
                "resources/sneat/assets/vendor/css/core.css", // ⬅️ INI YANG BARU
                "resources/sneat/assets/vendor/css/theme-default.css",
                "resources/sneat/assets/css/demo.css",
                "resources/sneat/assets/vendor/css/pages/page-auth.css",
      ],
      refresh: true,
    }),
  ],
  resolve: {
    alias: {
      '~sneat': path.resolve(__dirname, 'resources/sneat/assets'),
    },
  },
})
