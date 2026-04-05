@extends('layouts.app', [
    'title' => $topic->title,
    'heading' => $topic->title,
    'subheading' => 'Topic details, registration list, presentation schedule, report uploads, and seminar scoring.',
])

@section('content')
    <section class="page-intro">
        <div>
            <div class="kicker-nav">
                <a href="{{ route('topics.index') }}">Topics</a>
                <span>/</span>
                <span class="active">{{ $topic->title }}</span>
            </div>
            <h2>{{ $topic->title }}</h2>
            <p class="muted">Topic details, seminar administration, registrations, uploaded reports, presentation schedule, and final scoring in one screen.</p>
        </div>
        <span class="badge {{ $topic->status }}">{{ $topic->status }}</span>
    </section>

    <div class="grid two">
        <section class="card">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Topic Overview</span>
                    <h2>Topic information</h2>
                </div>
                <span class="badge">{{ $topic->registrations->count() }} registrations</span>
            </div>

            <p>{{ $topic->description }}</p>

            <div class="meta-grid">
                <div><span class="label">Lecturer</span><strong>{{ $topic->lecturer->name }}</strong></div>
                <div><span class="label">Registrations</span><strong>{{ $topic->registrations->count() }}</strong></div>
            </div>

            @if ((auth()->user()->isLecturer() && auth()->id() === $topic->lecturer_id) || auth()->user()->isAdmin())
                <div class="inline-actions wrap-actions">
                    <a href="{{ route('topics.edit', $topic) }}" class="button">Edit topic</a>
                    <a href="{{ route('topics.summary', $topic) }}" class="button secondary">Print summary</a>
                    <form action="{{ route('topics.destroy', $topic) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this topic?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button danger">Delete</button>
                    </form>
                </div>
            @endif
        </section>

        <section class="card">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Registrations</span>
                    <h2>Registration list</h2>
                </div>
            </div>

            <div class="stack-list">
                @forelse ($topic->registrations as $registration)
                    <div class="list-item wide">
                        <div>
                            <strong>{{ $registration->student->name }}</strong>
                            <div class="muted small">{{ $registration->student->email }}</div>
                            @if ($registration->submission)
                                <div class="muted small">
                                    Report: <a href="{{ route('submissions.download', $registration->submission) }}">{{ $registration->submission->original_name }}</a>
                                </div>
                                @if ($registration->submission->note)
                                    <div class="muted small">Note: {{ $registration->submission->note }}</div>
                                @endif
                                <div class="muted small">
                                    Review status: {{ str_replace('_', ' ', $registration->submission->review_status) }}
                                    · Revision {{ $registration->submission->revision_number }}
                                </div>
                                @if ($registration->submission->review_note)
                                    <div class="note-box">
                                        <div class="label">Review note</div>
                                        <p class="muted small">{{ $registration->submission->review_note }}</p>
                                        <div class="muted small">
                                            Reviewed by {{ $registration->submission->reviewer?->name ?? 'Lecturer' }}
                                            @if ($registration->submission->reviewed_at)
                                                · {{ $registration->submission->reviewed_at->format('d/m/Y H:i') }}
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif
                            @if ($registration->presentation)
                                <div class="muted small">Presentation: {{ $registration->presentation->scheduled_at->format('d/m/Y H:i') }} · {{ $registration->presentation->room }}</div>
                            @endif
                            @if ($registration->score)
                                <div class="muted small">Score: {{ number_format($registration->score->score, 2) }}/10</div>
                                @if ($registration->score->comment)
                                    <div class="muted small">{{ $registration->score->comment }}</div>
                                @endif
                            @endif
                        </div>

                        <div class="action-column">
                            <span class="badge {{ $registration->status }}">{{ $registration->status }}</span>

                            @if ((auth()->user()->isLecturer() && auth()->id() === $topic->lecturer_id) || auth()->user()->isAdmin())
                                <form action="{{ route('registrations.update-status', $registration) }}" method="POST" class="inline-actions">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status">
                                        <option value="pending" @selected($registration->status === 'pending')>Pending</option>
                                        <option value="approved" @selected($registration->status === 'approved')>Approved</option>
                                        <option value="rejected" @selected($registration->status === 'rejected')>Rejected</option>
                                    </select>
                                    <button type="submit" class="button small">Save</button>
                                </form>

                                @if ($registration->submission)
                                    <form action="{{ route('submissions.review', $registration->submission) }}" method="POST" class="form compact-form">
                                        @csrf
                                        @method('PATCH')
                                        <label>
                                            <span>Review status</span>
                                            <select name="review_status" required>
                                                <option value="changes_requested" @selected(old('review_status', $registration->submission->review_status) === 'changes_requested')>Changes requested</option>
                                                <option value="accepted" @selected(old('review_status', $registration->submission->review_status) === 'accepted')>Accepted</option>
                                            </select>
                                        </label>
                                        <label>
                                            <span>Review note</span>
                                            <textarea name="review_note" rows="3" required>{{ old('review_note', $registration->submission->review_note) }}</textarea>
                                        </label>
                                        <button type="submit" class="button secondary small">Save review</button>
                                    </form>
                                @endif

                                @if ($registration->status === 'approved')
                                    <div class="inline-actions">
                                        <a href="{{ $registration->presentation ? route('presentations.edit', $registration->presentation) : route('presentations.create', $registration) }}" class="button secondary small">
                                            {{ $registration->presentation ? 'Edit schedule' : 'Create schedule' }}
                                        </a>
                                    </div>

                                    <form action="{{ $registration->score ? route('scores.update', $registration->score) : route('scores.store', $registration) }}" method="POST" class="form compact-form">
                                        @csrf
                                        @if ($registration->score)
                                            @method('PUT')
                                        @endif
                                        <label>
                                            <span>Score</span>
                                            <input type="number" name="score" min="0" max="10" step="0.1" value="{{ old('score', $registration->score?->score) }}" required>
                                        </label>
                                        <label>
                                            <span>Comment</span>
                                            <textarea name="comment" rows="3">{{ old('comment', $registration->score?->comment) }}</textarea>
                                        </label>
                                        <button type="submit" class="button small">{{ $registration->score ? 'Update score' : 'Save score' }}</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="muted">No students have registered for this topic yet.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="card spaced-card">
        <div class="section-head">
            <div>
                <span class="eyebrow">Timeline</span>
                <h2>Topic activity</h2>
            </div>
        </div>

        <div class="stack-list">
            @forelse ($activities as $activity)
                <article class="list-item wide activity-item">
                    <div>
                        <strong>{{ $activity->description }}</strong>
                        <div class="muted small">{{ $activity->user?->name ?? 'System' }} · {{ $activity->created_at->diffForHumans() }}</div>
                    </div>
                    <span class="badge">{{ str_replace('.', ' ', $activity->action) }}</span>
                </article>
            @empty
                <p class="muted">No activity recorded for this topic yet.</p>
            @endforelse
        </div>
    </section>
@endsection
