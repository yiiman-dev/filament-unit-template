import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";
import collectModuleAssetsPaths from './vite-module-loader.js';

export default defineConfig(async () => {
    // Collect all unit asset paths
    const unitPaths = await collectModuleAssetsPaths([], './Modules');

    // Filter out Blade templates and keep only JS/CSS/asset files for build
    const buildPaths = unitPaths.filter(path =>
        !path.includes('*.blade.php') &&
        !path.includes('**/*.blade.php') &&
        (path.endsWith('blade.php') || path.endsWith('.js') || path.endsWith('.css') || path.endsWith('.scss') || path.endsWith('.sass'))
    );

    // Add main app file
    const allInputs = ['resources/js/app.js', ...buildPaths];

    console.log('Building with inputs:', allInputs);

    return defineConfig({
        plugins: [
            laravel({
                input: allInputs,
                refresh: [
                    `resources/views/**/*`,
                    'Modules/Units/Auth/My/resources/views/filament/layout/auth.blade.php',
                    `Modules/Units/**/resources/views/*`,
                    `Modules/Units/**/resources/views/widgets/*`,
                    `Modules/Units/**/resources/views/components/*`,
                    `Modules/Units/**/Common/resources/views/*`,
                    `Modules/Units/**/Common/resources/views/widgets/*`,
                    `Modules/Units/**/Common/resources/views/components/*`,
                    `Modules/Units/**/Common/resources/views/livewire/*`,

                    `Modules/Units/**/**/resources/views/*`,
                    `Modules/Units/**/**/resources/views/widgets/*`,
                    `Modules/Units/**/**/resources/views/components/*`,
                    `Modules/Units/**/**/Common/resources/views/*`,
                    `Modules/Units/**/**/Common/resources/views/components/*`,
                    `Modules/Units/**/**/Common/resources/views/widgets/*`,

                    `Modules/Units/**/**/**/resources/views/*`,
                    `Modules/Units/**/**/**/resources/views/widgets/*`,
                    `Modules/Units/**/**/**/resources/views/components/*`,
                    `Modules/Units/**/**/**/Common/resources/views/*`,
                    `Modules/Units/**/**/**/Common/resources/views/components/*`,
                    `Modules/Units/**/**/**/Common/resources/views/widgets/*`,
                ],
            }),
            tailwindcss(),
        ],
        server: {
            cors: true,
        },
    });
});
