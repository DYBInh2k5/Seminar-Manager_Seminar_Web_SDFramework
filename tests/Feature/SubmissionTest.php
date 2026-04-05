<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_upload_report_for_their_registration(): void
    {
        Storage::fake('local');
        Mail::fake();

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
        $this->assertSame('submitted', $registration->submission->review_status);
        $this->assertSame(1, $registration->submission->revision_number);
        Storage::disk('local')->assertExists($registration->submission->file_path);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'submission.uploaded',
            'user_id' => $student->id,
        ]);
    }

    public function test_student_can_delete_their_uploaded_report(): void
    {
        Storage::fake('local');
        Mail::fake();

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

    public function test_lecturer_can_review_submission_with_feedback(): void
    {
        Mail::fake();

        $student = User::factory()->create(['role' => 'student']);
        $lecturer = User::factory()->create(['role' => 'lecturer']);
        $topic = Topic::create([
            'title' => 'Review flow topic',
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
            'original_name' => 'draft-report.pdf',
            'file_path' => 'seminar-reports/draft-report.pdf',
            'mime_type' => 'application/pdf',
            'submitted_at' => now(),
            'note' => 'Draft',
            'review_status' => 'submitted',
            'revision_number' => 1,
        ]);

        $response = $this->actingAs($lecturer)->patch(route('submissions.review', $submission), [
            'review_status' => 'changes_requested',
            'review_note' => 'Please strengthen the evaluation section and clarify the methodology.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Submission review saved successfully.');

        $submission->refresh();

        $this->assertSame('changes_requested', $submission->review_status);
        $this->assertSame('Please strengthen the evaluation section and clarify the methodology.', $submission->review_note);
        $this->assertSame($lecturer->id, $submission->reviewed_by);
        $this->assertNotNull($submission->reviewed_at);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'submission.reviewed',
            'user_id' => $lecturer->id,
            'subject_id' => $submission->id,
        ]);
    }

    public function test_resubmission_increments_revision_and_clears_previous_review(): void
    {
        Storage::fake('local');
        Mail::fake();

        $student = User::factory()->create(['role' => 'student']);
        $lecturer = User::factory()->create(['role' => 'lecturer']);
        $topic = Topic::create([
            'title' => 'Resubmission topic',
            'description' => 'A long enough description for validation to pass in the topic setup.',
            'lecturer_id' => $lecturer->id,
            'status' => 'open',
        ]);

        $registration = Registration::create([
            'topic_id' => $topic->id,
            'student_id' => $student->id,
            'status' => 'approved',
        ]);

        $registration->submission()->create([
            'original_name' => 'old-report.pdf',
            'file_path' => 'seminar-reports/old-report.pdf',
            'mime_type' => 'application/pdf',
            'submitted_at' => now()->subDay(),
            'note' => 'Old draft',
            'review_status' => 'changes_requested',
            'review_note' => 'Add a stronger conclusion.',
            'reviewed_by' => $lecturer->id,
            'reviewed_at' => now()->subHours(8),
            'revision_number' => 1,
        ]);

        Storage::disk('local')->put('seminar-reports/old-report.pdf', 'old');

        $response = $this->actingAs($student)->post(route('submissions.store', $registration), [
            'report' => UploadedFile::fake()->create('new-report.pdf', 300, 'application/pdf'),
            'note' => 'Updated draft after feedback.',
        ]);

        $response->assertRedirect();

        $registration->refresh();
        $submission = $registration->submission;

        $this->assertSame('new-report.pdf', $submission->original_name);
        $this->assertSame('submitted', $submission->review_status);
        $this->assertNull($submission->review_note);
        $this->assertNull($submission->reviewed_by);
        $this->assertNull($submission->reviewed_at);
        $this->assertSame(2, $submission->revision_number);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'submission.resubmitted',
            'user_id' => $student->id,
        ]);
    }
}
