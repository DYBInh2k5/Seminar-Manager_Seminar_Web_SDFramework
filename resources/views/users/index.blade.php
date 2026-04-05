@extends('layouts.app', [
    'title' => 'Users',
    'heading' => 'User management',
    'subheading' => 'Create accounts, assign roles, and manage seminar access.',
])

@section('content')
    <section class="page-intro">
        <div>
            <div class="kicker-nav">
                <span>Administration</span>
                <span>/</span>
                <span class="active">User Directory</span>
            </div>
            <h2>User management</h2>
            <p class="muted">Create, review, and maintain accounts for administrators, lecturers, and students from a single academic directory.</p>
        </div>
        <a href="{{ route('users.create') }}" class="button">
            <span class="material-symbols-outlined">person_add</span>
            <span>Create user</span>
        </a>
    </section>

    <section class="card filter-card">
        <form action="{{ route('users.index') }}" method="GET" class="filter-grid users-filter-grid">
            <label>
                <span>Search</span>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search by name or email">
            </label>

            <label>
                <span>Role</span>
                <select name="role">
                    <option value="">All roles</option>
                    <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>Admin</option>
                    <option value="lecturer" @selected(($filters['role'] ?? '') === 'lecturer')>Lecturer</option>
                    <option value="student" @selected(($filters['role'] ?? '') === 'student')>Student</option>
                </select>
            </label>

            <label>
                <span>Department</span>
                <select name="department">
                    <option value="">All departments</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department }}" @selected(($filters['department'] ?? '') === $department)>{{ $department }}</option>
                    @endforeach
                </select>
            </label>

            <div class="inline-actions filter-actions">
                <button type="submit" class="button">Apply filters</button>
                <a href="{{ route('users.index') }}" class="button secondary">Reset</a>
            </div>
        </form>
    </section>

    <section class="card">
        <div class="section-head">
            <div>
                <span class="eyebrow">Admin</span>
                <h2>All users</h2>
            </div>
            <span class="badge">{{ $users->count() }} shown</span>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Academic profile</th><th>Role</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                {{ $user->name }}
                                @if ($user->student_code)
                                    <div class="muted small">{{ $user->student_code }}</div>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <div>{{ $user->department ?: 'No department' }}</div>
                                @if ($user->cohort)
                                    <div class="muted small">{{ $user->cohort }}</div>
                                @endif
                            </td>
                            <td><span class="badge">{{ $user->role }}</span></td>
                            <td class="actions">
                                <a href="{{ route('users.edit', $user) }}" class="button secondary small">Edit</a>
                                @if (! auth()->user()->is($user))
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button danger small">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
