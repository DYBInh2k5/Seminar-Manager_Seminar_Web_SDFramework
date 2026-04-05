<?php

namespace App\Http\Controllers;

use App\Models\AiChatConversation;
use App\Support\SeminarAiChat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            ],
        ]);
    }

    public function store(Request $request, SeminarAiChat $chat): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
            'conversation_id' => ['nullable', 'integer'],
        ]);

        try {
            $user = $request->user();
            $conversation = $user->aiChatConversations()->find($validated['conversation_id'] ?? 0);

            if (! $conversation) {
                $conversation = $user->aiChatConversations()->create([
                    'title' => Str::limit($validated['message'], 60, '...'),
                ]);
            }

            $conversation->messages()->create([
                'role' => 'user',
                'content' => $validated['message'],
            ]);

            $result = $chat->reply(
                $user,
                $validated['message'],
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
                'title' => $conversation->title ?: Str::limit($validated['message'], 60, '...'),
                'last_response_id' => $result['response_id'] ?? $conversation->last_response_id,
            ])->save();

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
            ]);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 503);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'The AI assistant is temporarily unavailable.',
            ], 500);
        }
    }

    public function createConversation(Request $request): JsonResponse
    {
        $conversation = $request->user()->aiChatConversations()->create([
            'title' => 'New conversation',
        ]);

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
}
