<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaTest extends TestCase
{
    use RefreshDatabase;

    public function test_manifest_is_valid_and_references_existing_icons(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('Taskly', $manifest['name']);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertNotEmpty($manifest['start_url']);

        $purposes = collect($manifest['icons'])->pluck('purpose')->all();
        $this->assertContains('any', $purposes);
        $this->assertContains('maskable', $purposes);

        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists(public_path(ltrim($icon['src'], '/')));
            [$width, $height] = getimagesize(public_path(ltrim($icon['src'], '/')));
            $this->assertSame($icon['sizes'], "{$width}x{$height}");
        }
    }

    public function test_service_worker_and_offline_page_exist(): void
    {
        $serviceWorker = file_get_contents(public_path('sw.js'));

        $this->assertStringContainsString("addEventListener('fetch'", $serviceWorker);
        $this->assertStringContainsString('/offline.html', $serviceWorker);
        $this->assertFileExists(public_path('offline.html'));
        $this->assertFileExists(public_path('icons/apple-touch-icon.png'));
    }

    public function test_guest_layout_links_the_manifest(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('rel="manifest"', false)
            ->assertSee('name="theme-color"', false)
            ->assertSee('rel="apple-touch-icon"', false);
    }

    public function test_app_layout_links_the_manifest(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('rel="manifest"', false)
            ->assertSee('name="theme-color"', false);
    }
}
