import React, { useMemo, useState } from 'react';

const STARTER_PROMPTS = [
    'Explain the seminar registration flow.',
    'How does scoring work in this project?',
    'Summarize the database design for me.',
];

function MessageBubble({ message }) {
    return (
        <article className={`chat-bubble ${message.role}`}>
            <div className="chat-bubble-meta">
                <strong>{message.role === 'assistant' ? 'SeminarBoost AI' : 'You'}</strong>
            </div>
            <div className="chat-bubble-body">
                {message.text.split('\n').map((line, index) => (
                    <p key={`${message.id}-${index}`}>{line}</p>
                ))}
            </div>
        </article>
    );
}

function buildWelcomeMessage(user) {
    return {
        id: 'welcome',
        role: 'assistant',
        text: `Hello ${user.name}. I can help with seminar workflow, project structure, topics, scoring, and how this Laravel app works.`,
    };
}

export default function AiChat({ endpoint, conversationEndpoint, showEndpointTemplate, bootstrap }) {
    const { user, conversations: initialConversations = [], activeConversation = null } = bootstrap;
    const [messages, setMessages] = useState(
        activeConversation?.messages?.length ? activeConversation.messages : [buildWelcomeMessage(user)],
    );
    const [conversations, setConversations] = useState(initialConversations);
    const [activeConversationId, setActiveConversationId] = useState(activeConversation?.id ?? null);
    const [draft, setDraft] = useState('');
    const [isSending, setIsSending] = useState(false);
    const [error, setError] = useState('');
    const [isLoadingConversation, setIsLoadingConversation] = useState(false);

    const canSend = useMemo(() => draft.trim().length > 0 && !isSending, [draft, isSending]);

    const sendMessage = async (text) => {
        const cleaned = text.trim();

        if (! cleaned) {
            return;
        }

        const userMessage = {
            id: `user-${Date.now()}`,
            role: 'user',
            text: cleaned,
        };

        setMessages((current) => [...current, userMessage]);
        setDraft('');
        setError('');
        setIsSending(true);

        try {
            const response = await window.axios.post(endpoint, {
                message: cleaned,
                conversation_id: activeConversationId,
            });

            setActiveConversationId(response.data.conversation?.id ?? activeConversationId);
            setConversations((current) => {
                const conversation = response.data.conversation;

                if (! conversation) {
                    return current;
                }

                const next = current.filter((item) => item.id !== conversation.id);

                return [
                    {
                        id: conversation.id,
                        title: conversation.title,
                        updatedAt: new Date().toISOString(),
                    },
                    ...next,
                ];
            });
            setMessages((current) => [
                ...current,
                {
                    id: response.data.message?.id ?? response.data.response_id ?? `assistant-${Date.now()}`,
                    role: 'assistant',
                    text: response.data.reply ?? 'The assistant returned an empty response.',
                },
            ]);
        } catch (requestError) {
            const message = requestError.response?.data?.message ?? 'Unable to contact the AI assistant right now.';

            setError(message);
        } finally {
            setIsSending(false);
        }
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        await sendMessage(draft);
    };

    const createConversation = async () => {
        setError('');
        setIsLoadingConversation(true);

        try {
            const response = await window.axios.post(conversationEndpoint);

            setActiveConversationId(response.data.id);
            setConversations((current) => [
                {
                    id: response.data.id,
                    title: response.data.title,
                    updatedAt: new Date().toISOString(),
                },
                ...current,
            ]);
            setMessages([buildWelcomeMessage(user)]);
        } catch (requestError) {
            const message = requestError.response?.data?.message ?? 'Unable to create a new conversation.';
            setError(message);
        } finally {
            setIsLoadingConversation(false);
        }
    };

    const loadConversation = async (conversationId) => {
        if (! conversationId || conversationId === activeConversationId) {
            return;
        }

        setError('');
        setIsLoadingConversation(true);

        try {
            const showEndpoint = showEndpointTemplate.replace('__CONVERSATION__', conversationId);
            const response = await window.axios.get(showEndpoint);

            setActiveConversationId(response.data.id);
            setMessages(response.data.messages?.length ? response.data.messages : [buildWelcomeMessage(user)]);
        } catch (requestError) {
            const message = requestError.response?.data?.message ?? 'Unable to load that conversation.';
            setError(message);
        } finally {
            setIsLoadingConversation(false);
        }
    };

    const resetConversation = async () => {
        setDraft('');
        await createConversation();
    };

    return (
        <div className="grid single ai-chat-grid">
            <section className="card">
                <div className="section-head">
                    <div>
                        <span className="eyebrow">History</span>
                        <h2>Saved conversations</h2>
                    </div>
                    <button type="button" className="button secondary small" onClick={createConversation} disabled={isSending || isLoadingConversation}>
                        New chat
                    </button>
                </div>

                <div className="stack-list">
                    {conversations.length > 0 ? (
                        conversations.map((conversation) => (
                            <button
                                key={conversation.id}
                                type="button"
                                className={`prompt-card history-card ${activeConversationId === conversation.id ? 'active' : ''}`}
                                onClick={() => loadConversation(conversation.id)}
                                disabled={isSending || isLoadingConversation}
                            >
                                <strong>{conversation.title}</strong>
                            </button>
                        ))
                    ) : (
                        <p className="muted">No saved conversations yet. Start the first one below.</p>
                    )}
                </div>
            </section>

            <section className="card">
                <div className="section-head">
                    <div>
                        <span className="eyebrow">Assistant</span>
                        <h2>Ask anything about Seminar Manager</h2>
                    </div>
                    <span className="badge role">{user.role}</span>
                </div>

                <div className="chat-transcript">
                    {messages.map((message) => (
                        <MessageBubble key={message.id} message={message} />
                    ))}
                </div>

                {error ? <div className="alert danger">{error}</div> : null}

                <form className="form chat-form" onSubmit={handleSubmit}>
                    <label>
                        <span>Your message</span>
                        <textarea
                            name="message"
                            rows="4"
                            placeholder="Ask about the registration flow, scoring, database design, or how this Laravel project works..."
                            value={draft}
                            onChange={(event) => setDraft(event.target.value)}
                        />
                    </label>

                    <div className="actions wrap-actions">
                        <button type="submit" className="button" disabled={!canSend}>
                            {isSending ? 'Thinking...' : 'Send to AI'}
                        </button>
                        <button type="button" className="button secondary" onClick={resetConversation} disabled={isSending || isLoadingConversation}>
                            New conversation
                        </button>
                    </div>
                </form>
            </section>

            <section className="card">
                <div className="section-head">
                    <div>
                        <span className="eyebrow">Suggestions</span>
                        <h2>Good starter prompts</h2>
                    </div>
                </div>

                <div className="stack-list">
                    {STARTER_PROMPTS.map((prompt) => (
                        <button
                            key={prompt}
                            type="button"
                            className="prompt-card"
                            onClick={() => sendMessage(prompt)}
                            disabled={isSending}
                        >
                            {prompt}
                        </button>
                    ))}
                </div>

                <div className="card compact chat-note">
                    <div className="label">What this assistant is for</div>
                    <p className="muted small">
                        It can explain project workflow, roles, routes, database structure, seminar usage, and general guidance for this system.
                    </p>
                </div>
            </section>
        </div>
    );
}
