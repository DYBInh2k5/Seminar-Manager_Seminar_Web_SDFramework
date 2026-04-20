<?php

namespace App\Http\Controllers;

use App\Models\AiChatConversation;
use App\Support\ActivityLogger;
use App\Support\SeminarAiChat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class AiChatController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $conversations = $user->aiChatConversations()->latest()->take(12)->get();
        $activeConversation = $conversations->first();

        return view('ai-chat', [
            'title' => 'AI Chat',
            'heading' => 'Seminar AI chat',
            'subheading' => 'Ask about seminar topics, registrations, scoring, or how this Laravel project works.',
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'chatBootstrap' => [
                'user' => [
                    'name' => $user->name,
                    'role' => $user->role,
                ],
                'conversations' => $conversations->map(fn (AiChatConversation $conversation) => [
                    'id' => $conversation->id,
                    'title' => $conversation->title ?: 'New conversation',
                    'updatedAt' => optional($conversation->updated_at)->toIso8601String(),
                ])->values(),
                'activeConversation' => $activeConversation ? [
                    'id' => $activeConversation->id,
                    'title' => $activeConversation->title ?: 'New conversation',
                    'messages' => $activeConversation->messages()
                        ->oldest()
                        ->get()
                        ->map(fn ($message) => [
                            'id' => $message->id,
                            'role' => $message->role,
                            'text' => $message->content,
                        ])->values(),
                ] : null,
                'quickActions' => $this->quickActions($user),
            ],
        ]);
    }

    public function store(Request $request, SeminarAiChat $chat): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:4000'],
            'action' => ['nullable', Rule::in(array_keys($this->quickActionMap($request->user())))],
            'conversation_id' => ['nullable', 'integer'],
        ]);

        $message = trim((string) ($validated['message'] ?? ''));
        $action = $validated['action'] ?? null;

        if ($message === '' && ! $action) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Please enter a message or choose a quick action.',
                ], 422);
            }

            return back()->with('status', 'Please enter a message or choose a quick action.');
        }

        $rateLimitKey = 'ai-chat:' . $request->user()->id;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 12)) {
            return response()->json([
                'message' => 'AI chat is receiving requests too quickly. Please wait a moment and try again.',
            ], 429);
        }

        try {
            $user = $request->user();
            $effectiveMessage = $this->resolveMessage($user, $message, $action);
            $conversation = $user->aiChatConversations()->find($validated['conversation_id'] ?? 0);

            if (! $conversation) {
                $conversation = $user->aiChatConversations()->create([
                    'title' => Str::limit($effectiveMessage, 60, '...'),
                ]);
            }

            $conversation->messages()->create([
                'role' => 'user',
                'content' => $effectiveMessage,
                'meta' => [
                    'action' => $action,
                ],
            ]);

            $result = $chat->reply(
                $user,
                $effectiveMessage,
                $conversation->last_response_id,
            );

            $assistantMessage = $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $result['reply'],
                'response_id' => $result['response_id'] ?? null,
                'meta' => [
                    'model' => $result['model'] ?? null,
                ],
            ]);

            $conversation->forceFill([
                'title' => $conversation->title ?: Str::limit($effectiveMessage, 60, '...'),
                'last_response_id' => $result['response_id'] ?? $conversation->last_response_id,
            ])->save();

            ActivityLogger::log(
                $user,
                'ai_chat.message_sent',
                "{$user->name} used the AI assistant" . ($action ? " with action {$action}" : '') . '.',
                $conversation,
                [
                    'action_name' => $action,
                    'user_role' => $user->role,
                ]
            );

            RateLimiter::hit($rateLimitKey, 60);

            if ($request->expectsJson()) {
                return response()->json([
                    'reply' => $result['reply'],
                    'response_id' => $result['response_id'] ?? null,
                    'model' => $result['model'] ?? null,
                    'conversation' => [
                        'id' => $conversation->id,
                        'title' => $conversation->title ?: 'New conversation',
                    ],
                    'message' => [
                        'id' => $assistantMessage->id,
                        'role' => 'assistant',
                        'text' => $assistantMessage->content,
                    ],
                    'effective_message' => $effectiveMessage,
                ]);
            }

            return back()->with('status', 'AI reply saved to the conversation.');
        } catch (RuntimeException $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 503);
            }

            return back()->with('status', $exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The AI assistant is temporarily unavailable.',
                ], 500);
            }

            return back()->with('status', 'The AI assistant is temporarily unavailable.');
        }
    }

    public function createConversation(Request $request): JsonResponse
    {
        $conversation = $request->user()->aiChatConversations()->create([
            'title' => 'New conversation',
        ]);

        ActivityLogger::log(
            $request->user(),
            'ai_chat.conversation_created',
            "{$request->user()->name} started a new AI chat conversation.",
            $conversation,
            [
                'user_role' => $request->user()->role,
            ]
        );

        return response()->json([
            'id' => $conversation->id,
            'title' => $conversation->title,
            'messages' => [],
        ], 201);
    }

    public function showConversation(Request $request, AiChatConversation $conversation): JsonResponse
    {
        abort_unless($conversation->user_id === $request->user()->id, 403);

        return response()->json([
            'id' => $conversation->id,
            'title' => $conversation->title ?: 'New conversation',
            'messages' => $conversation->messages()
                ->oldest()
                ->get()
                ->map(fn ($message) => [
                    'id' => $message->id,
                    'role' => $message->role,
                    'text' => $message->content,
                ])->values(),
        ]);
    }

    protected function quickActions($user): array
    {
        return collect($this->quickActionMap($user))
            ->map(fn (array $action, string $key) => [
                'id' => $key,
                'label' => $action['label'],
                'description' => $action['description'],
            ])
            ->values()
            ->all();
    }

    protected function quickActionMap($user): array
    {
        $common = [
            'project_overview' => [
                'label' => 'Project overview',
                'description' => 'Summarize the structure, workflow, and core features of Seminar Manager.',
                'prompt' => 'Give me a concise overview of the Seminar Manager project, including its main modules and workflow.',
            ],
            'database_summary' => [
                'label' => 'Database summary',
                'description' => 'Explain the important database tables and how they relate to one another.',
                'prompt' => 'Explain the database design of this Laravel seminar project, focusing on the core tables and relationships.',
            ],
        ];

        $roleSpecific = match ($user->role) {
            'student' => [
                'my_registrations' => [
                    'label' => 'My registrations',
                    'description' => 'Summarize my registrations, report status, presentation schedule, and score.',
                    'prompt' => 'Summarize my current seminar registrations, report review status, presentation schedule, and scores.',
                ],
                'resubmission_help' => [
                    'label' => 'Resubmission help',
                    'description' => 'Explain how report review notes and resubmission work in this system.',
                    'prompt' => 'Explain how report review notes, change requests, and resubmission work for students in this seminar system.',
                ],
            ],
            'lecturer' => [
                'pending_reviews' => [
                    'label' => 'Pending reviews',
                    'description' => 'Explain what I should review next as a lecturer.',
                    'prompt' => 'Based on my lecturer role, summarize my pending registrations, report review responsibilities, and scheduling tasks.',
                ],
                'feedback_tips' => [
                    'label' => 'Feedback tips',
                    'description' => 'Suggest a good review workflow for report feedback and scoring.',
                    'prompt' => 'Suggest a practical lecturer workflow for reviewing reports, requesting changes, accepting submissions, scheduling presentations, and publishing scores.',
                ],
            ],
            'admin' => [
                'system_health' => [
                    'label' => 'System health',
                    'description' => 'Summarize the current workflow health of the seminar platform.',
                    'prompt' => 'Summarize the current system health of the seminar platform, including registrations, report reviews, upcoming presentations, and user roles.',
                ],
                'demo_script' => [
                    'label' => 'Demo script',
                    'description' => 'Create a quick walkthrough for presenting this project in class.',
                    'prompt' => 'Create a short live-demo walkthrough for presenting this Seminar Manager project to a lecturer or class.',
                ],
            ],
            default => [],
        };

        return $common + $roleSpecific;
    }

    protected function resolveMessage($user, string $message, ?string $action): string
    {
        if (! $action) {
            return $message;
        }

        $prompt = $this->quickActionMap($user)[$action]['prompt'] ?? '';

        return trim($prompt . ($message !== '' ? "\n\nExtra user request: {$message}" : ''));
    }
}
