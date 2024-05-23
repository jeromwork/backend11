import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    build: {
        outDir: '../../public/build-health',
        emptyOutDir: true,
        manifest: true,
    },
    plugins: [
        laravel({
            publicDirectory: '../../public',
            buildDirectory: 'build-health',
            input: [
                __dirname + '/resources/assets/sass/app.scss',
                __dirname + '/resources/assets/js/app.js'
            ],
            refresh: true,
        }),
    ],
});

//export const paths = [
//    'Modules/Health/resources/assets/sass/app.scss',
//    'Modules/Health/resources/assets/js/app.js',
//];