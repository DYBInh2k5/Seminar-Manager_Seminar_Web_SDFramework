@extends('layouts.app', [
    'title' => 'Seminar Topics',
    'heading' => 'Seminar topic list',
    'subheading' => 'Manage topics, registrations, and seminar progress in one place.',
])

@section('content')
    <section class="card filter-card">
        <form action="{{ route('topics.index') }}" method="GET" class="filter-grid">
            <label>
                <span>Search</span>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search by title or description">
            </label>

            <label>
                <span>Status</span>
                <select name="status">
                    <option value="">All statuses</option>
                    <option value="open" @selected(($filters['status'] ?? '') === 'open')>Open</option>
                    <option value="closed" @selected(($filters['status'] ?? '') === 'closed')>Closed</option>
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
            @if (auth()->user()->isLecturer() || auth()->user()->isAdmin())
                <a href="{{ route('topics.create') }}" class="button">Create topic</a>
            @endif
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Topic</th><th>Lecturer</th><th>Status</th><th>Registrations</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($topics as $topic)
                        <tr>
                            <td>
                                <strong>{{ $topic->title }}</strong>
                                <div class="muted small">{{ \Illuminate\Support\Str::limit($topic->description, 90) }}</div>
                            </td>
                            <td>{{ $topic->lecturer->name }}</td>
                            <td><span class="badge {{ $topic->status }}">{{ $topic->status }}</span></td>
                            <td>{{ $topic->registrations_count }}</td>
                            <td class="actions">
                                <a href="{{ route('topics.show', $topic) }}" class="button secondary small">Details</a>
                                @if (auth()->user()->isStudent())
                                    <form action="{{ route('registrations.store', $topic) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="button small" {{ $topic->status !== 'open' ? 'disabled' : '' }}>Register</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">No topics match your current filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
