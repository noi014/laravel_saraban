import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
   //  base: '/my-app-laravel12/public/',
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [`resources/views/**/*`],
        }),
        tailwindcss(),
    ],
     base: '/build/', // สำคัญมากเมื่อ deploy
     build: {
        outDir: 'public/build',
        //manifest: true,        // ✅ สำคัญ!
        rollupOptions: {
            input: 'resources/js/app.js',
        },
    },
    server: {
        cors: true,
    },
});