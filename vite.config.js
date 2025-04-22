import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        // Configuración de Laravel Vite Plugin
        laravel({
            input: 'resources/js/app.js',  // Archivo de entrada para el bundle
            refresh: true,  // Activar la recarga en caliente
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,  // Evitar la base URL predeterminada
                    includeAbsolute: false,  // No incluir rutas absolutas en los assets
                },
            },
        }),
    ],
});
