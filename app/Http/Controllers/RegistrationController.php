<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Topic;
use App\Support\ActivityLogger;
use App\Support\SeminarNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function store(Request $request, Topic $topic): RedirectResponse
    {
        abort_unless($request->user()->isStudent(), 403);

        if ($topic->status !== 'open') {
            return back()->with('status', 'This topic is closed for registration.');
        }

        [$registration, $created] = tap(Registration::firstOrCreate(
            [
                'topic_id' => $topic->id,
                'student_id' => $request->user()->id,
            ],
            [
                'status' => 'pending',
            ]
        ), fn ($registration) => $registration->load(['topic.lecturer', 'student']))->pipe(fn ($registration) => [$registration, $registration->wasRecentlyCreated]);

        if ($created) {
            SeminarNotifier::registrationSubmitted($registration);
            ActivityLogger::log(
                $request->user(),
                'registration.submitted',
                "{$request->user()->name} registered for {$topic->title}.",
                $registration,
                [
                    'topic_id' => $topic->id,
                    'student_id' => $request->user()->id,
                    'lecturer_id' => $topic->lecturer_id,
                    'registration_status' => $registration->status,
                ]
            );
        }

        return back()->with('status', $created ? 'Your topic registration has been submitted.' : 'You have already registered for this topic.');
    }

    public function updateStatus(Request $request, Registration $registration): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || ($user->isLecturer() && $registration->topic->lecturer_id === $user->id), 403);

        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected,pending'],
        ]);

        $registration->update($data);
        $registration->load(['topic', 'student']);
        SeminarNotifier::registrationStatusUpdated($registration);
        ActivityLogger::log(
            $user,
            'registration.status_updated',
            "{$user->name} set {$registration->student->name}'s registration for {$registration->topic->title} to {$registration->status}.",
            $registration,
            [
                'topic_id' => $registration->topic_id,
                'student_id' => $registration->student_id,
                'lecturer_id' => $registration->topic->lecturer_id,
                'registration_status' => $registration->status,
            ]
        );

        return back()->with('status', 'Registration status updated successfully.');
    }
}
