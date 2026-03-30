<?php

namespace Tests\Feature;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_a_lecturer_when_creating_a_topic(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lecturer = User::factory()->create(['role' => 'lecturer']);

        $response = $this->actingAs($admin)->post(route('topics.store'), [
            'title' => 'Admin-created seminar topic',
            'description' => 'This description is long enough to satisfy the validation rules for a seminar topic.',
            'status' => 'open',
            'lecturer_id' => $lecturer->id,
        ]);

        $response->assertRedirect(route('topics.index'));
        $this->assertDatabaseHas('topics', [
            'title' => 'Admin-created seminar topic',
            'lecturer_id' => $lecturer->id,
        ]);
    }

    public function test_topic_list_can_be_filtered_by_search_term_and_status(): void
    {
        $lecturer = User::factory()->create(['role' => 'lecturer']);

        Topic::create([
            'title' => 'Laravel Boost Seminar',
            'description' => 'A searchable topic used to verify the seminar topic filtering feature.',
            'lecturer_id' => $lecturer->id,
            'status' => 'open',
        ]);

        Topic::create([
            'title' => 'Closed legacy topic',
            'description' => 'This topic should not appear when open seminar topics are filtered.',
            'lecturer_id' => $lecturer->id,
            'status' => 'closed',
        ]);

        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get(route('topics.index', [
            'q' => 'Laravel Boost',
            'status' => 'open',
        ]));

        $response->assertOk();
        $response->assertSee('Laravel Boost Seminar');
        $response->assertDontSee('Closed legacy topic');
    }

    public function test_lecturer_can_open_printable_topic_summary(): void
    {
        $lecturer = User::factory()->create(['role' => 'lecturer']);
        $topic = Topic::create([
            'title' => 'Printable summary topic',
            'description' => 'A printable topic summary should render as an export-friendly HTML page.',
            'lecturer_id' => $lecturer->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($lecturer)->get(route('topics.summary', $topic));

        $response->assertOk();
        $response->assertSee('Print / Save as PDF');
        $response->assertSee('Printable summary topic');
    }
}
