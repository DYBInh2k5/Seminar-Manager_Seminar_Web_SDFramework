@extends('layouts.app', [
    'title' => 'AI Chat',
    'heading' => 'Seminar AI chat',
    'subheading' => 'Ask the built-in assistant about workflow, project structure, or seminar support.',
])

@section('content')
    <section class="page-intro">
        <div>
            <div class="kicker-nav">
                <span>Assistant</span>
                <span>/</span>
                <span class="active">AI Conversation</span>
            </div>
            <h2>Seminar AI assistant</h2>
            <p class="muted">Review saved conversations, ask role-aware questions, and use the assistant to understand workflow, architecture, and seminar progress.</p>
        </div>
        <span class="badge role">{{ auth()->user()->role }}</span>
    </section>

    <section
        id="ai-chat-root"
        class="ai-chat-shell"
        data-endpoint="{{ route('ai-chat.store') }}"
        data-conversation-endpoint="{{ route('ai-chat.conversations.store') }}"
        data-show-endpoint-template="{{ url('/ai-chat/conversations/__CONVERSATION__') }}"
        data-bootstrap='@json($chatBootstrap)'
    >
        <section class="card">
            <div class="section-head">
                <div>
                    <span class="eyebrow">AI Chat</span>
                    <h2>Assistant is loading...</h2>
                </div>
            </div>
            <p class="muted">Start the frontend assets with Vite to use the chat interface.</p>
        </section>
    </section>
@endsection
