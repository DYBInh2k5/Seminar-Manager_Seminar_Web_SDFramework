<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'New Lecturer',
            'email' => 'new-lecturer@example.com',
            'role' => 'lecturer',
            'department' => 'Software Engineering',
            'cohort' => 'Faculty',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'new-lecturer@example.com',
            'role' => 'lecturer',
            'department' => 'Software Engineering',
        ]);
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get(route('users.index'));

        $response->assertForbidden();
    }
}
