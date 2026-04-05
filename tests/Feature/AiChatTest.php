<?php

namespace Tests\Feature;

use App\Models\AiChatConversation;
use App\Models\User;
use App\Support\SeminarAiChat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AiChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_ai_chat_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('ai-chat.index'));

        $response->assertOk();
        $response->assertSee('Seminar AI chat');
    }

    public function test_ai_chat_endpoint_returns_mocked_reply(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $mock = Mockery::mock(SeminarAiChat::class);
        $mock->shouldReceive('reply')
            ->once()
            ->withArgs(fn (User $actor, string $message, ?string $previousResponseId) => $actor->is($user)
                && $message === 'Explain the scoring flow.'
                && $previousResponseId === null)
            ->andReturn([
                'reply' => 'Lecturers create or update the final score from the registration record.',
                'response_id' => 'resp_test_123',
                'model' => 'gpt-4.1-mini',
            ]);

        $this->instance(SeminarAiChat::class, $mock);

        $response = $this->actingAs($user)->postJson(route('ai-chat.store'), [
            'message' => 'Explain the scoring flow.',
        ]);

        $response->assertOk();
        $response->assertJson([
            'reply' => 'Lecturers create or update the final score from the registration record.',
            'response_id' => 'resp_test_123',
            'model' => 'gpt-4.1-mini',
        ]);

        $this->assertDatabaseHas('ai_chat_conversations', [
            'user_id' => $user->id,
            'title' => 'Explain the scoring flow.',
            'last_response_id' => 'resp_test_123',
        ]);

        $conversation = AiChatConversation::query()->where('user_id', $user->id)->firstOrFail();

        $this->assertDatabaseHas('ai_chat_messages', [
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'Explain the scoring flow.',
        ]);

        $this->assertDatabaseHas('ai_chat_messages', [
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => 'Lecturers create or update the final score from the registration record.',
            'response_id' => 'resp_test_123',
        ]);
    }

    public function test_user_can_open_their_saved_conversation(): void
    {
        $user = User::factory()->create();
        $conversation = $user->aiChatConversations()->create([
            'title' => 'Registration help',
        ]);

        $conversation->messages()->createMany([
            [
                'role' => 'user',
                'content' => 'How do I register?',
            ],
            [
                'role' => 'assistant',
                'content' => 'Open a topic and click the register button.',
            ],
        ]);

        $response = $this->actingAs($user)->getJson(route('ai-chat.conversations.show', $conversation));

        $response->assertOk();
        $response->assertJsonPath('title', 'Registration help');
        $response->assertJsonCount(2, 'messages');
    }

    public function test_user_cannot_open_someone_elses_saved_conversation(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $conversation = $owner->aiChatConversations()->create([
            'title' => 'Private help',
        ]);

        $response = $this->actingAs($otherUser)->getJson(route('ai-chat.conversations.show', $conversation));

        $response->assertForbidden();
    }
}
