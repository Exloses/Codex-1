import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        cssCodeSplit: true,
        sourcemap: false,
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (!id.includes('node_modules')) {
                        return;
                    }

                    if (id.includes('@inertiajs') || id.includes('ziggy')) {
                        return 'vendor-inertia';
                    }

                    if (id.includes('swiper')) {
                        return 'vendor-swiper';
                    }

                    if (id.includes('@headlessui') || id.includes('@heroicons')) {
                        return 'vendor-ui';
                    }

                    if (id.includes('vue')) {
                        return 'vendor-vue';
                    }
                },
            },
        },
    },
    optimizeDeps: {
        include: ['@inertiajs/vue3', 'vue', '@vue/server-renderer'],
    },
});
