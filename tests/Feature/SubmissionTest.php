<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_upload_report_for_their_registration(): void
    {
        Storage::fake('local');

        $student = User::factory()->create(['role' => 'student']);
        $lecturer = User::factory()->create(['role' => 'lecturer']);
        $topic = Topic::create([
            'title' => 'AI-assisted seminar workflow',
            'description' => 'A long enough description for validation to pass in the topic setup.',
            'lecturer_id' => $lecturer->id,
            'status' => 'open',
        ]);

        $registration = Registration::create([
            'topic_id' => $topic->id,
            'student_id' => $student->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($student)->post(route('submissions.store', $registration), [
            'report' => UploadedFile::fake()->create('seminar-report.pdf', 256, 'application/pdf'),
            'note' => 'First submission for review.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Report uploaded successfully.');

        $registration->refresh();

        $this->assertNotNull($registration->submission);
        $this->assertSame('seminar-report.pdf', $registration->submission->original_name);
        $this->assertSame('First submission for review.', $registration->submission->note);
        Storage::disk('local')->assertExists($registration->submission->file_path);
    }

    public function test_student_can_delete_their_uploaded_report(): void
    {
        Storage::fake('local');

        $student = User::factory()->create(['role' => 'student']);
        $lecturer = User::factory()->create(['role' => 'lecturer']);
        $topic = Topic::create([
            'title' => 'Report deletion flow',
            'description' => 'A long enough description for validation to pass in the topic setup.',
            'lecturer_id' => $lecturer->id,
            'status' => 'open',
        ]);

        $registration = Registration::create([
            'topic_id' => $topic->id,
            'student_id' => $student->id,
            'status' => 'approved',
        ]);

        $submission = $registration->submission()->create([
            'original_name' => 'seminar-report.pdf',
            'file_path' => 'seminar-reports/seminar-report.pdf',
            'mime_type' => 'application/pdf',
            'submitted_at' => now(),
            'note' => 'Temporary file',
        ]);

        Storage::disk('local')->put($submission->file_path, 'demo');

        $response = $this->actingAs($student)->delete(route('submissions.destroy', $submission));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Report deleted successfully.');
        $this->assertDatabaseMissing('submissions', ['id' => $submission->id]);
    }
}
