<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_is_public(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('Privacy Policy');
    }

    public function test_login_screen_links_to_privacy_policy(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('privacy'), false);
    }

    public function test_privacy_policy_is_translated_to_serbian(): void
    {
        $this->withSession(['locale' => 'sr'])
            ->get(route('privacy'))
            ->assertOk()
            ->assertSee('Politika privatnosti');
    }

    public function test_generated_urls_use_https_behind_a_tls_terminating_proxy(): void
    {
        $response = $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Host' => 'taskly.onrender.com',
        ])->get(route('login'));

        $response->assertOk();
        $this->assertStringContainsString(
            'https://taskly.onrender.com/auth/google/redirect',
            $response->getContent(),
        );
    }

    public function test_health_check_endpoint_responds(): void
    {
        $this->get('/up')->assertOk();
    }
}
