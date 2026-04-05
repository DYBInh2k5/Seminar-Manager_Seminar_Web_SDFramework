<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $activities = ActivityLog::query()
            ->with('user')
            ->when(! $user->isAdmin(), function (Builder $query) use ($user) {
                $query->where(function (Builder $inner) use ($user) {
                    $inner->where('user_id', $user->id);

                    if ($user->isStudent()) {
                        $inner->orWhere('metadata->student_id', $user->id);
                    }

                    if ($user->isLecturer()) {
                        $inner->orWhere('metadata->lecturer_id', $user->id);
                    }
                });
            })
            ->latest()
            ->paginate(20);

        return view('activity.index', [
            'activities' => $activities,
            'title' => 'Activity Logs',
            'heading' => 'Activity logs',
            'subheading' => 'Track the latest seminar actions, reviews, schedules, and score updates.',
        ]);
    }
}
