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
        <section class="grid single ai-chat-grid ai-chat-fallback">
            <section class="card">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">History</span>
                        <h2>Saved conversations</h2>
                    </div>
                </div>

                <div class="stack-list">
                    @forelse ($conversations as $conversation)
                        <article class="prompt-card history-card @if($activeConversation && $activeConversation->id === $conversation->id) active @endif">
                            <strong>{{ $conversation->title ?: 'New conversation' }}</strong>
                        </article>
                    @empty
                        <p class="muted">No saved conversations yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="card">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Assistant</span>
                        <h2>Ask anything about Seminar Manager</h2>
                    </div>
                    <span class="badge role">{{ auth()->user()->role }}</span>
                </div>

                <div class="chat-transcript fallback-transcript">
                    @forelse(($activeConversation?->messages ?? collect()) as $message)
                        <article class="chat-bubble {{ $message->role }}">
                            <div class="chat-bubble-meta">
                                <strong>{{ $message->role === 'assistant' ? 'SeminarBoost AI' : 'You' }}</strong>
                            </div>
                            <div class="chat-bubble-body">
                                <p>{{ $message->content }}</p>
                            </div>
                        </article>
                    @empty
                        <article class="chat-bubble assistant">
                            <div class="chat-bubble-meta">
                                <strong>SeminarBoost AI</strong>
                            </div>
                            <div class="chat-bubble-body">
                                <p>Hello {{ auth()->user()->name }}, use the form below to ask about topics, registrations, report reviews, scoring, or the project structure.</p>
                            </div>
                        </article>
                    @endforelse
                </div>

                @if (session('status'))
                    <div class="alert success">{{ session('status') }}</div>
                @endif

                <form class="form chat-form" method="POST" action="{{ route('ai-chat.store') }}">
                    @csrf
                    @if ($activeConversation)
                        <input type="hidden" name="conversation_id" value="{{ $activeConversation->id }}">
                    @endif
                    <label>
                        <span>Your message</span>
                        <textarea
                            name="message"
                            rows="4"
                            placeholder="Ask about the registration flow, report review, scoring, database design, or how this Laravel project works..."
                            required
                        ></textarea>
                    </label>

                    <div class="actions wrap-actions">
                        <button type="submit" class="button">
                            Send to AI
                        </button>
                    </div>
                </form>
            </section>

            <section class="card">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Quick actions</span>
                        <h2>Role-aware prompts</h2>
                    </div>
                </div>

                <p class="muted">Enable Vite to use the interactive buttons below. The fallback form above still works without JavaScript.</p>
            </section>
        </section>
    </section>
@endsection
