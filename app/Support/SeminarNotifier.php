<?php

namespace App\Support;

use App\Models\Presentation;
use App\Models\Registration;
use App\Models\Score;
use App\Models\Submission;
use Illuminate\Support\Facades\Mail;

class SeminarNotifier
{
    public static function registrationSubmitted(Registration $registration): void
    {
        $topic = $registration->topic;
        $student = $registration->student;
        $lecturer = $topic->lecturer;

        Mail::raw(
            "A student has registered for your seminar topic.\n\nStudent: {$student->name}\nTopic: {$topic->title}\nStatus: {$registration->status}",
            fn ($message) => $message->to($lecturer->email)->subject('New seminar topic registration')
        );
    }

    public static function registrationStatusUpdated(Registration $registration): void
    {
        Mail::raw(
            "Your seminar registration has been updated.\n\nTopic: {$registration->topic->title}\nStatus: {$registration->status}",
            fn ($message) => $message->to($registration->student->email)->subject('Seminar registration status updated')
        );
    }

    public static function reportUploaded(Submission $submission): void
    {
        $registration = $submission->registration;
        Mail::raw(
            "A report has been uploaded for a seminar registration.\n\nStudent: {$registration->student->name}\nTopic: {$registration->topic->title}\nFile: {$submission->original_name}",
            fn ($message) => $message->to($registration->topic->lecturer->email)->subject('Seminar report uploaded')
        );
    }

    public static function reportDeleted(Registration $registration): void
    {
        Mail::raw(
            "A student has removed their uploaded seminar report.\n\nStudent: {$registration->student->name}\nTopic: {$registration->topic->title}",
            fn ($message) => $message->to($registration->topic->lecturer->email)->subject('Seminar report deleted')
        );
    }

    public static function presentationScheduled(Presentation $presentation): void
    {
        $registration = $presentation->registration;
        Mail::raw(
            "Your seminar presentation has been scheduled.\n\nTopic: {$registration->topic->title}\nTime: {$presentation->scheduled_at->format('d/m/Y H:i')}\nRoom: {$presentation->room}",
            fn ($message) => $message->to($registration->student->email)->subject('Seminar presentation scheduled')
        );
    }

    public static function scorePublished(Score $score): void
    {
        $registration = $score->registration;
        Mail::raw(
            "Your seminar score has been published.\n\nTopic: {$registration->topic->title}\nScore: {$score->score}/10\nComment: " . ($score->comment ?? 'No comment'),
            fn ($message) => $message->to($registration->student->email)->subject('Seminar score published')
        );
    }
}
