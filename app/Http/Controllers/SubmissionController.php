<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Submission;
use App\Support\SeminarNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    public function store(Request $request, Registration $registration): RedirectResponse
    {
        $this->authorizeStudent($request, $registration);

        $data = $request->validate([
            'report' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($registration->submission) {
            Storage::disk('local')->delete($registration->submission->file_path);
        }

        $file = $data['report'];
        $path = $file->store('seminar-reports', 'local');

        $submission = $registration->submission()->updateOrCreate([], [
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType() ?? $file->getMimeType() ?? 'application/octet-stream',
            'submitted_at' => now(),
            'note' => $data['note'] ?? null,
        ]);

        $submission->load('registration.topic.lecturer', 'registration.student');
        SeminarNotifier::reportUploaded($submission);

        return back()->with('status', 'Report uploaded successfully.');
    }

    public function download(Request $request, Submission $submission): StreamedResponse
    {
        $registration = $submission->registration;
        $user = $request->user();

        abort_unless(
            $user->isAdmin()
            || ($user->isLecturer() && $registration->topic->lecturer_id === $user->id)
            || ($user->isStudent() && $registration->student_id === $user->id),
            403
        );

        return Storage::disk('local')->download($submission->file_path, $submission->original_name);
    }

    public function destroy(Request $request, Submission $submission): RedirectResponse
    {
        $registration = $submission->registration;
        $user = $request->user();

        abort_unless(
            ($user->isStudent() && $registration->student_id === $user->id)
            || $user->isAdmin(),
            403
        );

        $path = $submission->file_path;

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        $submission->delete();
        $registration->load('topic.lecturer', 'student');
        SeminarNotifier::reportDeleted($registration);

        return back()->with('status', 'Report deleted successfully.');
    }

    protected function authorizeStudent(Request $request, Registration $registration): void
    {
        $user = $request->user();

        abort_unless(
            $user->isStudent()
            && $registration->student_id === $user->id
            && in_array($registration->status, ['pending', 'approved'], true),
            403
        );
    }
}
