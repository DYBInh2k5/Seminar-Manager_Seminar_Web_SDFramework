@extends('layouts.app', [
    'title' => 'Dashboard',
    'heading' => 'Seminar dashboard',
    'subheading' => 'Track topics, registrations, presentations, and reporting from one place.',
])

@section('content')
    <section class="page-intro">
        <div>
            <div class="kicker-nav">
                <span>Archives</span>
                <span>/</span>
                <span class="active">Overview</span>
            </div>
            <h2>Curated insights for the academic term</h2>
            <p class="muted">A stitched academic dashboard view for topics, registrations, reporting, and seminar performance.</p>
        </div>
        @if (auth()->user()->isLecturer() || auth()->user()->isAdmin())
            <a href="{{ route('topics.create') }}" class="button">
                <span class="material-symbols-outlined">add</span>
                <span>New seminar</span>
            </a>
        @endif
    </section>

    <section class="stat-grid">
        <article class="stat-card"><span>Topics</span><strong>{{ $stats['topics'] }}</strong></article>
        <article class="stat-card"><span>Students</span><strong>{{ $stats['students'] }}</strong></article>
        <article class="stat-card"><span>Pending</span><strong>{{ $stats['pending_registrations'] }}</strong></article>
        <article class="stat-card"><span>Approved</span><strong>{{ $stats['approved_registrations'] }}</strong></article>
    </section>

    <div class="grid two">
        <div
            id="dashboard-analytics-root"
            class="analytics-shell"
            data-dashboard='@json($dashboardAnalytics)'
            data-show-leaderboard='@json(! auth()->user()->isStudent())'
        >
            <div class="card">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Analytics</span>
                        <h2>Interactive dashboard is loading...</h2>
                    </div>
                </div>
                <p class="muted">If JavaScript is disabled, refresh the page after enabling assets.</p>
            </div>
        </div>
    </div>

    @if (! auth()->user()->isStudent())
        <section class="card spaced-card">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Leaderboard</span>
                    <h2>Top lecturers by approved registrations</h2>
                </div>
            </div>

            <div class="stack-list">
                @forelse ($topLecturers as $lecturer)
                    <div class="list-item">
                        <div>
                            <strong>{{ $lecturer->name }}</strong>
                            <div class="muted small">Topics: {{ $lecturer->topics_count }}</div>
                        </div>
                        <span class="badge approved">{{ $lecturer->approved_registrations_count }} approved</span>
                    </div>
                @empty
                    <p class="muted">No lecturer analytics available yet.</p>
                @endforelse
            </div>
        </section>
    @endif

    <div class="grid two">
        <section class="card">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Topics</span>
                    <h2>Recent topics</h2>
                </div>
                <a href="{{ route('topics.index') }}" class="button secondary">View all</a>
            </div>

            <div class="stack-list">
                @forelse ($myTopics as $topic)
                    <a href="{{ route('topics.show', $topic) }}" class="list-item">
                        <div>
                            <strong>{{ $topic->title }}</strong>
                            <div class="muted small">{{ $topic->lecturer->name }} · {{ $topic->registrations_count }} registrations</div>
                        </div>
                        <span class="badge {{ $topic->status }}">{{ $topic->status }}</span>
                    </a>
                @empty
                    <p class="muted">No topics available yet.</p>
                @endforelse
            </div>
        </section>

        @if (auth()->user()->isStudent())
            <section class="card">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Student</span>
                        <h2>My registrations</h2>
                    </div>
                </div>

                <div class="stack-list">
                    @forelse ($myRegistrations as $registration)
                        <div class="list-item wide">
                            <div>
                                <strong>{{ $registration->topic->title }}</strong>
                                <div class="muted small">Lecturer: {{ $registration->topic->lecturer->name }}</div>
                                @if ($registration->presentation)
                                    <div class="muted small">Presentation: {{ $registration->presentation->scheduled_at->format('d/m/Y H:i') }} · {{ $registration->presentation->room }}</div>
                                @endif
                                @if ($registration->submission)
                                    <div class="muted small">
                                        Report: <a href="{{ route('submissions.download', $registration->submission) }}">{{ $registration->submission->original_name }}</a>
                                    </div>
                                @endif
                                @if ($registration->score)
                                    <div class="muted small">Score: {{ number_format($registration->score->score, 2) }}/10</div>
                                @endif
                            </div>

                            <div class="action-column">
                                <span class="badge {{ $registration->status }}">{{ $registration->status }}</span>

                                @if ($registration->submission)
                                    <form action="{{ route('submissions.destroy', $registration->submission) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button danger small">Delete report</button>
                                    </form>
                                @endif

                                @if (in_array($registration->status, ['pending', 'approved'], true))
                                    <form action="{{ route('submissions.store', $registration) }}" method="POST" enctype="multipart/form-data" class="form compact-form">
                                        @csrf
                                        <label>
                                            <span>Upload report</span>
                                            <input type="file" name="report" accept=".pdf,.doc,.docx" required>
                                        </label>
                                        <label>
                                            <span>Note</span>
                                            <textarea name="note" rows="2">{{ old('note', $registration->submission?->note) }}</textarea>
                                        </label>
                                        <button type="submit" class="button small">{{ $registration->submission ? 'Replace report' : 'Upload report' }}</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="muted">You have not registered for any topics yet.</p>
                    @endforelse
                </div>
            </section>
        @else
            <section class="card">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Approval Queue</span>
                        <h2>Pending requests</h2>
                    </div>
                </div>

                <div class="stack-list">
                    @forelse ($pendingForReview as $registration)
                        <div class="list-item">
                            <div>
                                <strong>{{ $registration->student->name }}</strong>
                                <div class="muted small">{{ $registration->topic->title }}</div>
                            </div>
                            <form action="{{ route('registrations.update-status', $registration) }}" method="POST" class="inline-actions">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button class="button small">Approve</button>
                            </form>
                        </div>
                    @empty
                        <p class="muted">There are no pending registrations right now.</p>
                    @endforelse
                </div>
            </section>
        @endif
    </div>
@endsection
