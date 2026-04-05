@extends('layouts.app', [
    'title' => 'Presentation Schedule',
    'heading' => 'Update presentation schedule',
    'subheading' => 'Schedule the presentation for an approved student registration.',
])

@section('content')
    <section class="page-intro">
        <div>
            <div class="kicker-nav">
                <a href="{{ route('topics.show', $registration->topic) }}">Topic Detail</a>
                <span>/</span>
                <span class="active">Presentation Schedule</span>
            </div>
            <h2>Presentation schedule</h2>
            <p class="muted">Set the seminar time, room, and review context for {{ $registration->student->name }} under {{ $registration->topic->title }}.</p>
        </div>
        <span class="badge approved">Approved registration</span>
    </section>

    <section class="card form-shell">
        <div class="form-shell-copy">
            <span class="eyebrow">Presentation Planning</span>
            <h2>{{ $registration->student->name }} · {{ $registration->topic->title }}</h2>
            <p class="muted">Use this schedule card to prepare the presentation slot and keep the topic detail page aligned with the final seminar calendar.</p>
        </div>

        <form action="{{ isset($presentation) ? route('presentations.update', $presentation) : route('presentations.store', $registration) }}" method="POST" class="form">
            @csrf
            @isset($presentation)
                @method('PUT')
            @endisset

            <label>
                <span>Presentation time</span>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', isset($presentation) ? $presentation->scheduled_at->format('Y-m-d\\TH:i') : '') }}" required>
            </label>

            <label>
                <span>Room</span>
                <input type="text" name="room" value="{{ old('room', $presentation->room ?? '') }}" required>
            </label>

            <div class="inline-actions">
                <button type="submit" class="button">{{ isset($presentation) ? 'Update schedule' : 'Create schedule' }}</button>
                <a href="{{ route('topics.show', $registration->topic) }}" class="button secondary">Back</a>
            </div>
        </form>
    </section>
@endsection
