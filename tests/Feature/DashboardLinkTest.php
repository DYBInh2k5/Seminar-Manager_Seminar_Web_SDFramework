<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_new_seminar_link_points_to_create_topic_page(): void
    {
        $user = User::factory()->create(['role' => 'lecturer']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('http://127.0.0.1:8002/topics/create');
    }
}
