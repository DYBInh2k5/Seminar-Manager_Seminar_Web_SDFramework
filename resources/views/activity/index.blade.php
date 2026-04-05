@extends('layouts.app')

@section('content')
    <section class="page-intro">
        <div>
            <div class="kicker-nav">
                <span>Monitoring</span>
                <span>/</span>
                <span class="active">Activity Feed</span>
            </div>
            <h2>Recent platform activity</h2>
            <p class="muted">A chronological view of topic updates, report reviews, scheduling actions, and score publishing across the seminar workflow.</p>
        </div>
        <span class="badge">{{ $activities->total() }} events</span>
    </section>

    <section class="card">
        <div class="section-head">
            <div>
                <span class="eyebrow">Timeline</span>
                <h2>Latest actions</h2>
            </div>
        </div>

        <div class="stack-list">
            @forelse ($activities as $activity)
                <article class="list-item wide activity-item">
                    <div>
                        <strong>{{ $activity->description }}</strong>
                        <div class="muted small">
                            {{ $activity->user?->name ?? 'System' }} · {{ $activity->created_at->diffForHumans() }}
                        </div>
                        @if (! empty($activity->metadata['review_status']))
                            <div class="muted small">Review status: {{ str_replace('_', ' ', $activity->metadata['review_status']) }}</div>
                        @endif
                    </div>
                    <span class="badge">{{ str_replace('.', ' ', $activity->action) }}</span>
                </article>
            @empty
                <p class="muted">No activity has been recorded yet.</p>
            @endforelse
        </div>

        <div class="pagination-wrap">
            {{ $activities->links() }}
        </div>
    </section>
@endsection
