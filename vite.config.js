import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Yazı tipleri derleme sırasında indirilir ve kendi sunucumuzdan
            // servis edilir — çalışma anında harici istek yok.
            fonts: [
                bunny('Archivo', { weights: [400, 500, 600, 700, 800] }),
                bunny('Inter', { weights: [400, 500, 600] }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
