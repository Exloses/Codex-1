import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

const sensitiveNavigationDenylist = [
    /^\/account(?:\/|$)/,
    /^\/admin(?:\/|$)/,
    /^\/api(?:\/|$)/,
    /^\/auth(?:\/|$)/,
    /^\/cart(?:\/|$)/,
    /^\/checkout(?:\/|$)/,
    /^\/login(?:\/|$)/,
    /^\/logout(?:\/|$)/,
    /^\/payment(?:\/|$)/,
    /^\/register(?:\/|$)/,
    /^\/stripe(?:\/|$)/,
    /^\/vendor(?:\/|$)/,
];

export default defineConfig(({ isSsrBuild }) => ({
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
        !isSsrBuild && VitePWA({
            outDir: 'public',
            filename: 'sw.js',
            manifestFilename: 'manifest.webmanifest',
            injectRegister: false,
            registerType: 'autoUpdate',
            includeAssets: ['icons/icon-192.png', 'icons/icon-512.png', 'offline.html'],
            manifest: {
                name: 'GlobalDropship',
                short_name: 'GlobalDrop',
                description: 'Global multi-vendor dropship marketplace',
                start_url: '/',
                scope: '/',
                display: 'standalone',
                background_color: '#f8fafc',
                theme_color: '#059669',
                icons: [
                    {
                        src: '/icons/icon-192.png',
                        sizes: '192x192',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                    {
                        src: '/icons/icon-512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                ],
            },
            workbox: {
                cacheId: 'globaldrop',
                cleanupOutdatedCaches: true,
                clientsClaim: true,
                skipWaiting: true,
                navigateFallback: '/offline.html',
                navigateFallbackDenylist: sensitiveNavigationDenylist,
                globPatterns: [
                    'build/assets/**/*.{css,js}',
                    'icons/*.png',
                    'offline.html',
                ],
                runtimeCaching: [
                    {
                        urlPattern: /^https:\/\/res\.cloudinary\.com\/.*\.(?:avif|gif|jpe?g|png|svg|webp)$/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'cloudinary-images',
                            cacheableResponse: {
                                statuses: [0, 200],
                            },
                            expiration: {
                                maxAgeSeconds: 7 * 24 * 60 * 60,
                                maxEntries: 150,
                            },
                        },
                    },
                    {
                        urlPattern: /^https?:\/\/[^/]+\/build\/assets\/.*\.(?:css|js|woff2?)$/i,
                        handler: 'StaleWhileRevalidate',
                        options: {
                            cacheName: 'static-assets',
                            cacheableResponse: {
                                statuses: [0, 200],
                            },
                            expiration: {
                                maxAgeSeconds: 7 * 24 * 60 * 60,
                                maxEntries: 80,
                            },
                        },
                    },
                    {
                        urlPattern: /^https?:\/\/[^/]+\/(?:products(?:\/.*)?|categories(?:\/.*)?|$)$/i,
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'public-storefront-pages',
                            networkTimeoutSeconds: 3,
                            cacheableResponse: {
                                statuses: [200],
                            },
                            expiration: {
                                maxAgeSeconds: 60 * 60,
                                maxEntries: 30,
                            },
                        },
                    },
                    {
                        urlPattern: /^https?:\/\/[^/]+\/(?:account|admin|api|auth|cart|checkout|login|logout|payment|register|stripe|vendor)(?:\/.*)?$/i,
                        handler: 'NetworkOnly',
                        options: {
                            cacheName: 'sensitive-routes-network-only',
                        },
                    },
                ],
            },
            devOptions: {
                enabled: false,
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
}));
