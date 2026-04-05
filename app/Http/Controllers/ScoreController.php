<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Score;
use App\Support\ActivityLogger;
use App\Support\SeminarNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function store(Request $request, Registration $registration): RedirectResponse
    {
        $this->authorizeAccess($request, $registration);

        $data = $request->validate([
            'score' => ['required', 'numeric', 'between:0,10'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $score = $registration->score()->updateOrCreate([], $data);
        $score->load('registration.topic', 'registration.student');
        SeminarNotifier::scorePublished($score);
        ActivityLogger::log(
            $request->user(),
            'score.published',
            "{$request->user()->name} published a score for {$registration->student->name} on {$registration->topic->title}.",
            $score,
            [
                'topic_id' => $registration->topic_id,
                'student_id' => $registration->student_id,
                'lecturer_id' => $registration->topic->lecturer_id,
                'registration_id' => $registration->id,
                'score' => $score->score,
            ]
        );

        return redirect()->route('topics.show', $registration->topic)->with('status', 'Seminar score saved successfully.');
    }

    public function update(Request $request, Score $score): RedirectResponse
    {
        $this->authorizeAccess($request, $score->registration);

        $data = $request->validate([
            'score' => ['required', 'numeric', 'between:0,10'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $score->update($data);
        $score->load('registration.topic', 'registration.student');
        SeminarNotifier::scorePublished($score);
        ActivityLogger::log(
            $request->user(),
            'score.updated',
            "{$request->user()->name} updated the score for {$score->registration->student->name} on {$score->registration->topic->title}.",
            $score,
            [
                'topic_id' => $score->registration->topic_id,
                'student_id' => $score->registration->student_id,
                'lecturer_id' => $score->registration->topic->lecturer_id,
                'registration_id' => $score->registration->id,
                'score' => $score->score,
            ]
        );

        return redirect()->route('topics.show', $score->registration->topic)->with('status', 'Seminar score updated successfully.');
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
