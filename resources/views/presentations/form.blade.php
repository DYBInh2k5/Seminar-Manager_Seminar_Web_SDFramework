@extends('layouts.app', [
    'title' => 'Presentation Schedule',
    'heading' => 'Update presentation schedule',
    'subheading' => 'Schedule the presentation for an approved student registration.',
])

@section('content')
    <section class="card form-card">
        <div class="section-head">
            <div>
                <span class="eyebrow">Presentation Schedule</span>
                <h2>{{ $registration->student->name }} · {{ $registration->topic->title }}</h2>
            </div>
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
