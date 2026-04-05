<?php

namespace App\Http\Controllers;

use App\Models\Presentation;
use App\Models\Registration;
use App\Support\ActivityLogger;
use App\Support\SeminarNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PresentationController extends Controller
{
    public function create(Registration $registration): View
    {
        $this->authorizeAccess(request(), $registration);

        return view('presentations.form', compact('registration'));
    }

    public function store(Request $request, Registration $registration): RedirectResponse
    {
        $this->authorizeAccess($request, $registration);

        $data = $request->validate([
            'scheduled_at' => ['required', 'date'],
            'room' => ['required', 'string', 'max:255'],
        ]);

        $presentation = $registration->presentation()->updateOrCreate([], $data);
        $presentation->load('registration.topic', 'registration.student');
        SeminarNotifier::presentationScheduled($presentation);
        ActivityLogger::log(
            $request->user(),
            'presentation.scheduled',
            "{$request->user()->name} scheduled {$registration->student->name}'s presentation for {$registration->topic->title}.",
            $presentation,
            [
                'topic_id' => $registration->topic_id,
                'student_id' => $registration->student_id,
                'lecturer_id' => $registration->topic->lecturer_id,
                'registration_id' => $registration->id,
            ]
        );

        return redirect()->route('topics.show', $registration->topic)->with('status', 'Presentation schedule updated successfully.');
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(Presentation $presentation): View
    {
        $this->authorizeAccess(request(), $presentation->registration);
        $registration = $presentation->registration;

        return view('presentations.form', compact('registration', 'presentation'));
    }

    public function update(Request $request, Presentation $presentation): RedirectResponse
    {
        $this->authorizeAccess($request, $presentation->registration);

        $data = $request->validate([
            'scheduled_at' => ['required', 'date'],
            'room' => ['required', 'string', 'max:255'],
        ]);

        $presentation->update($data);
        $presentation->load('registration.topic', 'registration.student');
        SeminarNotifier::presentationScheduled($presentation);
        ActivityLogger::log(
            $request->user(),
            'presentation.updated',
            "{$request->user()->name} updated the presentation schedule for {$presentation->registration->student->name}.",
            $presentation,
            [
                'topic_id' => $presentation->registration->topic_id,
                'student_id' => $presentation->registration->student_id,
                'lecturer_id' => $presentation->registration->topic->lecturer_id,
                'registration_id' => $presentation->registration->id,
            ]
        );

        return redirect()->route('topics.show', $presentation->registration->topic)->with('status', 'Presentation schedule updated successfully.');
    }

    public function destroy(string $id)
    {
        abort(404);
    }

    protected function authorizeAccess(Request $request, Registration $registration): void
    {
        $user = $request->user();
        abort_unless(
            $registration->status === 'approved'
            && ($user->isAdmin() || ($user->isLecturer() && $registration->topic->lecturer_id === $user->id)),
            403
        );
    }
}
