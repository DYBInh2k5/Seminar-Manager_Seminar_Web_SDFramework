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
            'category' => 'Learning Analytics',
            'capacity' => 4,
            'semester' => 'Fall 2026',
            'difficulty' => 'intermediate',
            'expected_outcomes' => 'Build a polished analytics dashboard and explain the data story clearly.',
            'status' => 'open',
            'lecturer_id' => $lecturer->id,
        ]);

        $response->assertRedirect(route('topics.index'));
        $this->assertDatabaseHas('topics', [
            'title' => 'Admin-created seminar topic',
            'lecturer_id' => $lecturer->id,
            'category' => 'Learning Analytics',
            'capacity' => 4,
        ]);
    }

    public function test_topic_list_can_be_filtered_by_search_term_status_and_category(): void
    {
        $lecturer = User::factory()->create(['role' => 'lecturer']);

        Topic::create([
            'title' => 'Laravel Boost Seminar',
            'description' => 'A searchable topic used to verify the seminar topic filtering feature.',
            'category' => 'AI-assisted Development',
            'capacity' => 3,
            'semester' => 'Fall 2026',
            'difficulty' => 'intermediate',
            'lecturer_id' => $lecturer->id,
            'status' => 'open',
        ]);

        Topic::create([
            'title' => 'Closed legacy topic',
            'description' => 'This topic should not appear when open seminar topics are filtered.',
            'category' => 'Data Migration',
            'capacity' => 2,
            'semester' => 'Spring 2026',
            'difficulty' => 'advanced',
            'lecturer_id' => $lecturer->id,
            'status' => 'closed',
        ]);

        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get(route('topics.index', [
            'q' => 'Laravel Boost',
            'status' => 'open',
            'category' => 'AI-assisted Development',
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
            'category' => 'Academic Systems',
            'capacity' => 2,
            'semester' => 'Fall 2026',
            'difficulty' => 'beginner',
            'lecturer_id' => $lecturer->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($lecturer)->get(route('topics.summary', $topic));

        $response->assertOk();
        $response->assertSee('Print / Save as PDF');
        $response->assertSee('Printable summary topic');
    }

    public function test_student_cannot_register_when_topic_capacity_is_full(): void
    {
        $lecturer = User::factory()->create(['role' => 'lecturer']);
        $student = User::factory()->create(['role' => 'student']);
        $otherStudent = User::factory()->create(['role' => 'student']);

        $topic = Topic::create([
            'title' => 'Capacity limited topic',
            'description' => 'This topic verifies that the registration capacity cannot be exceeded by additional students.',
            'category' => 'Academic Systems',
            'capacity' => 1,
            'semester' => 'Fall 2026',
            'difficulty' => 'beginner',
            'lecturer_id' => $lecturer->id,
            'status' => 'open',
        ]);

        $topic->registrations()->create([
            'student_id' => $student->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($otherStudent)->post(route('registrations.store', $topic));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'This topic has reached its registration capacity.');

        $this->assertDatabaseCount('registrations', 1);
    }
}
