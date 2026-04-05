import React, { useMemo, useState } from 'react';

function escapeHtml(value) {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function renderInlineMarkdown(text) {
    const escaped = escapeHtml(text);

    return escaped
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
        .replace(/\*([^*]+)\*/g, '<em>$1</em>')
        .replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noreferrer">$1</a>');
}

function renderMarkdownBlocks(text) {
    const lines = text.split(/\r?\n/);
    const blocks = [];
    let paragraph = [];
    let listItems = [];
    let listType = null;

    const flushParagraph = () => {
        if (!paragraph.length) {
            return;
        }

        blocks.push({
            type: 'paragraph',
            content: paragraph.join(' '),
        });
        paragraph = [];
    };

    const flushList = () => {
        if (!listItems.length) {
            return;
        }

        blocks.push({
            type: listType,
            items: [...listItems],
        });
        listItems = [];
        listType = null;
    };

    lines.forEach((line) => {
        const trimmed = line.trim();

        if (!trimmed) {
            flushParagraph();
            flushList();
            return;
        }

        const headingMatch = trimmed.match(/^(#{1,3})\s+(.+)$/);
        if (headingMatch) {
            flushParagraph();
            flushList();
            blocks.push({
                type: 'heading',
                level: headingMatch[1].length,
                content: headingMatch[2],
            });
            return;
        }

        const unorderedMatch = trimmed.match(/^[-*]\s+(.+)$/);
        if (unorderedMatch) {
            flushParagraph();
            if (listType && listType !== 'ul') {
                flushList();
            }
            listType = 'ul';
            listItems.push(unorderedMatch[1]);
            return;
        }

        const orderedMatch = trimmed.match(/^\d+\.\s+(.+)$/);
        if (orderedMatch) {
            flushParagraph();
            if (listType && listType !== 'ol') {
                flushList();
            }
            listType = 'ol';
            listItems.push(orderedMatch[1]);
            return;
        }

        flushList();
        paragraph.push(trimmed);
    });

    flushParagraph();
    flushList();

    return blocks;
}

function MarkdownMessage({ text }) {
    const blocks = useMemo(() => renderMarkdownBlocks(text), [text]);

    return (
        <div className="chat-bubble-body markdown-body">
            {blocks.map((block, index) => {
                const key = `${block.type}-${index}`;

                if (block.type === 'heading') {
                    const Tag = `h${Math.min(block.level + 2, 6)}`;
                    return <Tag key={key} dangerouslySetInnerHTML={{ __html: renderInlineMarkdown(block.content) }} />;
                }

                if (block.type === 'ul' || block.type === 'ol') {
                    const Tag = block.type;
                    return (
                        <Tag key={key}>
                            {block.items.map((item, itemIndex) => (
                                <li key={`${key}-${itemIndex}`} dangerouslySetInnerHTML={{ __html: renderInlineMarkdown(item) }} />
                            ))}
                        </Tag>
                    );
                }

                return <p key={key} dangerouslySetInnerHTML={{ __html: renderInlineMarkdown(block.content) }} />;
            })}
        </div>
    );
}

function MessageBubble({ message }) {
    return (
        <article className={`chat-bubble ${message.role}`}>
            <div className="chat-bubble-meta">
                <strong>{message.role === 'assistant' ? 'SeminarBoost AI' : 'You'}</strong>
            </div>
            <MarkdownMessage text={message.text} />
        </article>
    );
}

function buildWelcomeMessage(user) {
    return {
        id: 'welcome',
        role: 'assistant',
        text: `# Welcome\nI can help **${user.name}** with seminar workflow, project structure, report reviews, scoring, and how this Laravel app works.`,
    };
}

export default function AiChat({ endpoint, conversationEndpoint, showEndpointTemplate, bootstrap }) {
    const {
        user,
        conversations: initialConversations = [],
        activeConversation = null,
        quickActions = [],
    } = bootstrap;

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

    const appendUserMessage = (text) => {
        setMessages((current) => [
            ...current,
            {
                id: `user-${Date.now()}`,
                role: 'user',
                text,
            },
        ]);
    };

    const updateConversationList = (conversation) => {
        if (!conversation) {
            return;
        }

        setConversations((current) => {
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
    };

    const sendPayload = async ({ message = '', action = null, previewText = '' }) => {
        if (!message.trim() && !action) {
            return;
        }

        if (previewText) {
            appendUserMessage(previewText);
        }

        setDraft('');
        setError('');
        setIsSending(true);

        try {
            const response = await window.axios.post(endpoint, {
                message,
                action,
                conversation_id: activeConversationId,
            });

            setActiveConversationId(response.data.conversation?.id ?? activeConversationId);
            updateConversationList(response.data.conversation);
            setMessages((current) => [
                ...current,
                {
                    id: response.data.message?.id ?? response.data.response_id ?? `assistant-${Date.now()}`,
                    role: 'assistant',
                    text: response.data.reply ?? 'The assistant returned an empty response.',
                },
            ]);
        } catch (requestError) {
            const messageText = requestError.response?.data?.message ?? 'Unable to contact the AI assistant right now.';
            setError(messageText);
        } finally {
            setIsSending(false);
        }
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        const cleaned = draft.trim();

        if (!cleaned) {
            return;
        }

        await sendPayload({ message: cleaned, previewText: cleaned });
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
            const messageText = requestError.response?.data?.message ?? 'Unable to create a new conversation.';
            setError(messageText);
        } finally {
            setIsLoadingConversation(false);
        }
    };

    const loadConversation = async (conversationId) => {
        if (!conversationId || conversationId === activeConversationId) {
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
            const messageText = requestError.response?.data?.message ?? 'Unable to load that conversation.';
            setError(messageText);
        } finally {
            setIsLoadingConversation(false);
        }
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
                            placeholder="Ask about the registration flow, report review, scoring, database design, or how this Laravel project works..."
                            value={draft}
                            onChange={(event) => setDraft(event.target.value)}
                        />
                    </label>

                    <div className="actions wrap-actions">
                        <button type="submit" className="button" disabled={!canSend}>
                            {isSending ? 'Thinking...' : 'Send to AI'}
                        </button>
                        <button type="button" className="button secondary" onClick={createConversation} disabled={isSending || isLoadingConversation}>
                            New conversation
                        </button>
                    </div>
                </form>
            </section>

            <section className="card">
                <div className="section-head">
                    <div>
                        <span className="eyebrow">Quick actions</span>
                        <h2>Role-aware prompts</h2>
                    </div>
                </div>

                <div className="stack-list">
                    {quickActions.map((action) => (
                        <button
                            key={action.id}
                            type="button"
                            className="prompt-card"
                            onClick={() => sendPayload({ action: action.id, previewText: action.label })}
                            disabled={isSending}
                        >
                            <strong>{action.label}</strong>
                            <div className="muted small">{action.description}</div>
                        </button>
                    ))}
                </div>

                <div className="card compact chat-note">
                    <div className="label">What this assistant is for</div>
                    <p className="muted small">
                        It can explain project workflow, roles, routes, database structure, report review flow, and practical guidance for this system.
                    </p>
                </div>
            </section>
        </div>
    );
}
