@extends('layouts.app', [
    'title' => 'Seminar Topics',
    'heading' => 'Seminar topic list',
    'subheading' => 'Manage topics, registrations, and seminar progress in one place.',
])

@section('content')
    <section class="page-intro">
        <div>
            <div class="kicker-nav">
                <span>Archives</span>
                <span>/</span>
                <span class="active">Curated Topics</span>
            </div>
            <h2>Seminar topics</h2>
            <p class="muted">Review faculty ownership, search the current archive, and manage topic intake with a cleaner academic control panel.</p>
        </div>
        @if (auth()->user()->isLecturer() || auth()->user()->isAdmin())
            <a href="{{ route('topics.create') }}" class="button">
                <span class="material-symbols-outlined">add</span>
                <span>Create topic</span>
            </a>
        @endif
    </section>

    <section class="card filter-card">
        <form action="{{ route('topics.index') }}" method="GET" class="filter-grid topics-filter-grid">
            <label>
                <span>Search</span>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search by title or description">
            </label>

            <label>
                <span>Category</span>
                <select name="category">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Status</span>
                <select name="status">
                    <option value="">All statuses</option>
                    <option value="open" @selected(($filters['status'] ?? '') === 'open')>Open</option>
                    <option value="closed" @selected(($filters['status'] ?? '') === 'closed')>Closed</option>
                </select>
            </label>

            <label>
                <span>Difficulty</span>
                <select name="difficulty">
                    <option value="">All levels</option>
                    <option value="beginner" @selected(($filters['difficulty'] ?? '') === 'beginner')>Beginner</option>
                    <option value="intermediate" @selected(($filters['difficulty'] ?? '') === 'intermediate')>Intermediate</option>
                    <option value="advanced" @selected(($filters['difficulty'] ?? '') === 'advanced')>Advanced</option>
                </select>
            </label>

            @if (! auth()->user()->isLecturer())
                <label>
                    <span>Lecturer</span>
                    <select name="lecturer_id">
                        <option value="">All lecturers</option>
                        @foreach ($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" @selected((string) ($filters['lecturer_id'] ?? '') === (string) $lecturer->id)>
                                {{ $lecturer->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
            @endif

            <div class="inline-actions filter-actions">
                <button type="submit" class="button">Apply filters</button>
                <a href="{{ route('topics.index') }}" class="button secondary">Reset</a>
            </div>
        </form>
    </section>

    <section class="card">
        <div class="section-head">
            <div>
                <span class="eyebrow">Seminar Topics</span>
                <h2>All topics</h2>
            </div>
            <span class="badge">{{ $topics->count() }} shown</span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Topic</th><th>Category</th><th>Lecturer</th><th>Status</th><th>Capacity</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($topics as $topic)
                        <tr>
                            <td>
                                <strong>{{ $topic->title }}</strong>
                                <div class="muted small">{{ \Illuminate\Support\Str::limit($topic->description, 90) }}</div>
                                <div class="muted small">{{ $topic->semester ?: 'Semester TBD' }} · {{ ucfirst($topic->difficulty) }}</div>
                            </td>
                            <td><span class="badge">{{ $topic->category }}</span></td>
                            <td>{{ $topic->lecturer->name }}</td>
                            <td><span class="badge {{ $topic->status }}">{{ $topic->status }}</span></td>
                            <td>{{ $topic->registrations_count }}/{{ $topic->capacity }}</td>
                            <td class="actions">
                                <a href="{{ route('topics.show', $topic) }}" class="button secondary small">Details</a>
                                @if (auth()->user()->isStudent())
                                    <form action="{{ route('registrations.store', $topic) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="button small" {{ $topic->status !== 'open' || $topic->registrations_count >= $topic->capacity ? 'disabled' : '' }}>Register</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="muted">No topics match your current filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
