<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExportController extends Controller
{
    public function topicSummary(Request $request, Topic $topic): View
    {
        $user = $request->user();
        abort_unless(
            $user->isAdmin() || ($user->isLecturer() && $topic->lecturer_id === $user->id),
            403
        );

        $topic->load([
            'lecturer',
            'registrations.student',
            'registrations.submission',
            'registrations.presentation',
            'registrations.score',
        ]);

        return view('exports.topic-summary', compact('topic'));
    }
}
