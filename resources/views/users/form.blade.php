<section class="card form-card">
    <form action="{{ $action }}" method="POST" class="form">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <label>
            <span>Name</span>
            <input type="text" name="name" value="{{ old('name', $user?->name) }}" required>
        </label>

        <label>
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email', $user?->email) }}" required>
        </label>

        <label>
            <span>Role</span>
            <select name="role">
                <option value="admin" @selected(old('role', $user?->role) === 'admin')>Admin</option>
                <option value="lecturer" @selected(old('role', $user?->role) === 'lecturer')>Lecturer</option>
                <option value="student" @selected(old('role', $user?->role) === 'student')>Student</option>
            </select>
        </label>

        <label>
            <span>Password {{ $user ? '(leave blank to keep current password)' : '' }}</span>
            <input type="password" name="password" {{ $user ? '' : 'required' }}>
        </label>

        <div class="inline-actions">
            <button type="submit" class="button">{{ $button }}</button>
            <a href="{{ route('users.index') }}" class="button secondary">Back</a>
        </div>
    </form>
</section>
