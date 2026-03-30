<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:open,closed'],
            'lecturer_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $topics = Topic::with([
                'lecturer',
                'registrations.student',
                'registrations.presentation',
                'registrations.score',
                'registrations.submission',
            ])
            ->withCount('registrations')
            ->when($user->isLecturer(), fn ($query) => $query->where('lecturer_id', $user->id))
            ->when($filters['q'] ?? null, function ($query, $term) {
                $query->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when(($filters['lecturer_id'] ?? null) && ! $user->isLecturer(), fn ($query, $lecturerId) => $query->where('lecturer_id', $lecturerId))
            ->latest()
            ->get();

        $lecturers = User::query()
            ->where('role', 'lecturer')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('topics.index', compact('topics', 'filters', 'lecturers'));
    }

    public function create(Request $request): View
    {
        return view('topics.create', [
            'lecturers' => $this->lecturersForForm($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['lecturer_id'] = $this->resolveLecturerId($request, $data['lecturer_id'] ?? null);

        Topic::create($data);

        return redirect()->route('topics.index')->with('status', 'Topic created successfully.');
    }

    public function show(Topic $topic): View
    {
        $topic->load([
            'lecturer',
            'registrations.student',
            'registrations.presentation',
            'registrations.score',
            'registrations.submission',
        ]);

        return view('topics.show', compact('topic'));
    }

    public function edit(Request $request, Topic $topic): View
    {
        $this->authorizeTopicAccess($request->user(), $topic);

        return view('topics.edit', [
            'topic' => $topic,
            'lecturers' => $this->lecturersForForm($request->user()),
        ]);
    }

    public function update(Request $request, Topic $topic): RedirectResponse
    {
        $this->authorizeTopicAccess($request->user(), $topic);

        $data = $this->validatedData($request);
        $data['lecturer_id'] = $this->resolveLecturerId($request, $data['lecturer_id'] ?? $topic->lecturer_id);

        $topic->update($data);

        return redirect()->route('topics.show', $topic)->with('status', 'Topic updated successfully.');
    }

    public function destroy(Request $request, Topic $topic): RedirectResponse
    {
        $this->authorizeTopicAccess($request->user(), $topic);

        $topic->delete();

        return redirect()->route('topics.index')->with('status', 'Topic deleted successfully.');
    }

    protected function authorizeTopicAccess(User $user, Topic $topic): void
    {
        if ($user->isAdmin()) {
            return;
        }

        abort_unless($user->isLecturer() && $topic->lecturer_id === $user->id, 403);
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'status' => ['required', 'in:open,closed'],
            'lecturer_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);
    }

    protected function resolveLecturerId(Request $request, ?int $lecturerId): int
    {
        if ($request->user()->isAdmin()) {
            return $lecturerId ?? User::query()->where('role', 'lecturer')->value('id');
        }

        return $request->user()->id;
    }

    protected function lecturersForForm(User $user)
    {
        if ($user->isAdmin()) {
            return User::query()->where('role', 'lecturer')->orderBy('name')->get(['id', 'name']);
        }

        return collect([$user->only(['id', 'name'])]);
    }
}
