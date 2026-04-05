<section class="card form-shell">
    <div class="form-shell-copy">
        <span class="eyebrow">Seminar Topic</span>
        <h2>{{ $topic ? 'Refine seminar details' : 'Open a new seminar topic' }}</h2>
        <p class="muted">
            {{ $topic
                ? 'Update the academic framing, registration availability, and assigned lecturer for this topic.'
                : 'Define the topic title, narrative, lecturer ownership, and registration status before students start applying.' }}
        </p>
    </div>

    <form action="{{ $action }}" method="POST" class="form">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <label>
            <span>Title</span>
            <input type="text" name="title" value="{{ old('title', $topic?->title) }}" required>
        </label>

        <label>
            <span>Description</span>
            <textarea name="description" rows="7" required>{{ old('description', $topic?->description) }}</textarea>
        </label>

        @if (auth()->user()->isAdmin())
            <label>
                <span>Assigned lecturer</span>
                <select name="lecturer_id" required>
                    <option value="">Select a lecturer</option>
                    @foreach ($lecturers as $lecturer)
                        <option value="{{ $lecturer->id }}" @selected((string) old('lecturer_id', $topic?->lecturer_id) === (string) $lecturer->id)>
                            {{ $lecturer->name }}
                        </option>
                    @endforeach
                </select>
            </label>
        @endif

        <label>
            <span>Status</span>
            <select name="status">
                <option value="open" @selected(old('status', $topic?->status ?? 'open') === 'open')>Open</option>
                <option value="closed" @selected(old('status', $topic?->status) === 'closed')>Closed</option>
            </select>
        </label>

        <div class="inline-actions">
            <button type="submit" class="button">{{ $button }}</button>
            <a href="{{ route('topics.index') }}" class="button secondary">Back</a>
        </div>
    </form>
</section>
