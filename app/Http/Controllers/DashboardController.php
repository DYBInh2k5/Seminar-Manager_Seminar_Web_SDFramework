<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Registration;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'topics' => Topic::count(),
            'students' => User::where('role', 'student')->count(),
            'pending_registrations' => Registration::where('status', 'pending')->count(),
            'approved_registrations' => Registration::where('status', 'approved')->count(),
            'open_slots' => max((int) Topic::query()->sum('capacity') - Registration::count(), 0),
        ];

        $statusBreakdown = Registration::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $roleBreakdown = User::query()
            ->selectRaw('role, count(*) as aggregate')
            ->groupBy('role')
            ->pluck('aggregate', 'role');

        $departmentBreakdown = User::query()
            ->whereNotNull('department')
            ->selectRaw('department, count(*) as aggregate')
            ->groupBy('department')
            ->orderByDesc('aggregate')
            ->pluck('aggregate', 'department');

        $categoryBreakdown = Topic::query()
            ->selectRaw('category, count(*) as aggregate')
            ->groupBy('category')
            ->orderByDesc('aggregate')
            ->pluck('aggregate', 'category');

        $topLecturers = User::query()
            ->where('role', 'lecturer')
            ->withCount('topics')
            ->withCount(['topics as approved_registrations_count' => function ($query) {
                $query->join('registrations', 'registrations.topic_id', '=', 'topics.id')
                    ->where('registrations.status', 'approved');
            }])
            ->orderByDesc('approved_registrations_count')
            ->take(5)
            ->get();

        $myTopics = Topic::withCount('registrations')
            ->with('lecturer')
            ->when($user->isLecturer(), fn ($query) => $query->where('lecturer_id', $user->id))
            ->latest()
            ->take(5)
            ->get();

        $myRegistrations = Registration::with(['topic.lecturer', 'presentation', 'score', 'submission'])
            ->where('student_id', $user->id)
            ->latest()
            ->get();

        $dashboardAnalytics = [
            'statusBreakdown' => [
                'pending' => (int) ($statusBreakdown['pending'] ?? 0),
                'approved' => (int) ($statusBreakdown['approved'] ?? 0),
                'rejected' => (int) ($statusBreakdown['rejected'] ?? 0),
            ],
            'roleBreakdown' => [
                'admin' => (int) ($roleBreakdown['admin'] ?? 0),
                'lecturer' => (int) ($roleBreakdown['lecturer'] ?? 0),
                'student' => (int) ($roleBreakdown['student'] ?? 0),
            ],
            'departmentBreakdown' => $departmentBreakdown->map(fn ($count, $department) => [
                'label' => $department,
                'value' => (int) $count,
            ])->values(),
            'categoryBreakdown' => $categoryBreakdown->map(fn ($count, $category) => [
                'label' => $category,
                'value' => (int) $count,
            ])->values(),
            'topLecturers' => $topLecturers->map(fn (User $lecturer) => [
                'name' => $lecturer->name,
                'topicsCount' => (int) $lecturer->topics_count,
                'approvedRegistrationsCount' => (int) $lecturer->approved_registrations_count,
            ])->values(),
        ];

        $pendingForReview = Registration::with(['topic', 'student'])
            ->where('status', 'pending')
            ->whereHas('topic', function ($query) use ($user) {
                if ($user->isLecturer()) {
                    $query->where('lecturer_id', $user->id);
                }
            })
            ->latest()
            ->get();

        $recentActivities = ActivityLog::query()
            ->with('user')
            ->when(! $user->isAdmin(), function ($query) use ($user) {
                $query->where(function ($inner) use ($user) {
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
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'stats',
            'statusBreakdown',
            'roleBreakdown',
            'topLecturers',
            'dashboardAnalytics',
            'myTopics',
            'myRegistrations',
            'pendingForReview',
            'recentActivities'
        ));
    }
}
