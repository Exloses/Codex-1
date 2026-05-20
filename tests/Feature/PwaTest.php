<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaTest extends TestCase
{
    public function test_pwa_icons_exist_and_are_valid_png_files(): void
    {
        foreach (['icon-192.png', 'icon-512.png'] as $icon) {
            $path = public_path("icons/{$icon}");

            $this->assertFileExists($path);
            $this->assertSame("\x89PNG\r\n\x1a\n", file_get_contents($path, false, null, 0, 8));
        }
    }

    public function test_offline_fallback_is_static_and_does_not_expose_private_fields(): void
    {
        $path = public_path('offline.html');

        $this->assertFileExists($path);

        $html = file_get_contents($path);

        $this->assertStringContainsString("You're offline", $html);
        $this->assertStringNotContainsString('vendor_price', $html);
        $this->assertStringNotContainsString('vendor_total_idr', $html);
        $this->assertStringNotContainsString('guest_email', $html);
        $this->assertStringNotContainsString('order_number', $html);
    }

    public function test_app_shell_references_manifest_and_pwa_theme_assets(): void
    {
        $blade = file_get_contents(resource_path('views/app.blade.php'));
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true);

        $this->assertStringContainsString('rel="manifest"', $blade);
        $this->assertStringContainsString('/manifest.webmanifest', $blade);
        $this->assertStringContainsString('name="theme-color"', $blade);
        $this->assertStringContainsString('/icons/icon-192.png', $blade);
        $this->assertSame('GlobalDropship', $manifest['name']);
        $this->assertSame('GlobalDrop', $manifest['short_name']);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('/', $manifest['start_url']);
        $this->assertSame('/', $manifest['scope']);
    }

    public function test_vite_pwa_config_keeps_ssr_safe_and_sensitive_routes_network_only(): void
    {
        $config = file_get_contents(base_path('vite.config.js'));

        $this->assertStringContainsString('VitePWA', $config);
        $this->assertStringContainsString('isSsrBuild', $config);
        $this->assertStringContainsString("filename: 'sw.js'", $config);
        $this->assertStringContainsString("manifestFilename: 'manifest.webmanifest'", $config);
        $this->assertStringContainsString('res\\.cloudinary\\.com', $config);
        $this->assertStringContainsString("handler: 'CacheFirst'", $config);
        $this->assertStringContainsString('navigateFallbackDenylist', $config);
        $this->assertStringContainsString('sensitive-routes-network-only', $config);
        $this->assertStringContainsString('checkout', $config);
        $this->assertStringContainsString('payment', $config);
        $this->assertStringContainsString('admin', $config);
        $this->assertStringContainsString('vendor', $config);
        $this->assertStringContainsString('account', $config);
    }
}
