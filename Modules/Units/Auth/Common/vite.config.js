import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

export default defineConfig({
    build: {
        outDir: '../../../public/build-auth',
        emptyOutDir: true,
        manifest: true,
    },
    plugins: [
        laravel({
            publicDirectory: '../../../public',
            buildDirectory: 'build-auth',
            input: [
                join(__dirname, './../Admin/resources/views/filament/layout/*.blade.php'),
                join(__dirname, './../Admin/resources/views/filament/pages/*.blade.php'),
                join(__dirname, './../Admin/resources/views/filament/pages/auth/*.blade.php'),
                join(__dirname, './../Manage/resources/views/*.blade.php'),
                join(__dirname, './../Manage/resources/views/filament/layout/auth/*.blade.php'),
                join(__dirname, './../Manage/resources/views/filament/pages/*.blade.php'),
                join(__dirname, './../Manage/resources/views/filament/pages/auth/*.blade.php'),
                join(__dirname, './../Manage/resources/assets/css/custom.css'),
                join(__dirname, './../Manage/resources/assets/sass/app.scss'),
                join(__dirname, './../Manage/resources/assets/js/app.js'),

                join(__dirname, './../My/resources/assets/css/custom.css'),
                join(__dirname, './../My/resources/assets/sass/app.scss'),
                join(__dirname, './../My/resources/assets/js/app.js'),


                join(__dirname, './../Manage/resources/views/*.blade.php'),
                join(__dirname, './../Manage/resources/views/filament/layout/auth/*.blade.php'),
                join(__dirname, './../Manage/resources/views/filament/layout/auth/register/*.blade.php'),
                join(__dirname, './../Manage/resources/views/filament/pages/*.blade.php'),
                join(__dirname, './../Manage/resources/views/filament/pages/auth/*.blade.php'),


            ],
            refresh: true,
        }),
    ],
});

// Export paths for the main vite config to collect
export const paths = [
    'Modules/Units/Auth/Admin/resources/views/filament/layout/*.blade.php',
    'Modules/Units/Auth/Admin/resources/views/filament/pages/*.blade.php',
    'Modules/Units/Auth/Admin/resources/views/filament/pages/auth/*.blade.php',
    'Modules/Units/Auth/Manage/resources/views/*.blade.php',
    'Modules/Units/Auth/Manage/resources/views/filament/layout/auth/*.blade.php',
    'Modules/Units/Auth/Manage/resources/views/filament/pages/*.blade.php',
    'Modules/Units/Auth/Manage/resources/views/filament/pages/auth/*.blade.php',
    'Modules/Units/Auth/Manage/resources/assets/css/custom.css',
    'Modules/Units/Auth/Manage/resources/assets/sass/app.scss',
    'Modules/Units/Auth/Manage/resources/assets/js/app.js',

    'Modules/Units/Auth/My/resources/assets/css/custom.css',
    'Modules/Units/Auth/My/resources/assets/sass/app.scss',
    'Modules/Units/Auth/My/resources/assets/js/app.js',


    'Modules/Units/Auth/Manage/resources/views/*.blade.php',
    'Modules/Units/Auth/Manage/resources/views/filament/layout/auth/*.blade.php',
    'Modules/Units/Auth/Manage/resources/views/filament/layout/auth/register/*.blade.php',
    'Modules/Units/Auth/Manage/resources/views/filament/pages/*.blade.php',
    'Modules/Units/Auth/Manage/resources/views/filament/pages/auth/*.blade.php',
];
