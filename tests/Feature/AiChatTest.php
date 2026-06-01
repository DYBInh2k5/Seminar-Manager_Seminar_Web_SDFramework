<?php

namespace Tests\Feature;

use App\Models\AiChatConversation;
use App\Models\User;
use App\Support\SeminarAiChat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
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
        $response->assertSee('Your message');
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

    public function test_quick_action_can_trigger_ai_chat_without_custom_message(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $mock = Mockery::mock(SeminarAiChat::class);
        $mock->shouldReceive('reply')
            ->once()
            ->withArgs(fn (User $actor, string $message) => $actor->is($user)
                && str_contains($message, 'Summarize my current seminar registrations'))
            ->andReturn([
                'reply' => 'You currently have one approved registration and one pending review.',
                'response_id' => 'resp_quick_action',
                'model' => 'gpt-4.1-mini',
            ]);

        $this->instance(SeminarAiChat::class, $mock);

        $response = $this->actingAs($user)->postJson(route('ai-chat.store'), [
            'action' => 'my_registrations',
        ]);

        $response->assertOk();
        $response->assertJsonPath('conversation.id', 1);
        $response->assertJsonPath('reply', 'You currently have one approved registration and one pending review.');
    }

    public function test_ai_chat_is_rate_limited(): void
    {
        $user = User::factory()->create();
        $rateLimitKey = 'ai-chat:'.$user->id;

        RateLimiter::clear($rateLimitKey);

        for ($i = 0; $i < 12; $i++) {
            RateLimiter::hit($rateLimitKey, 60);
        }

        $response = $this->actingAs($user)->postJson(route('ai-chat.store'), [
            'message' => 'Will this be blocked?',
        ]);

        $response->assertStatus(429);
        $response->assertJsonPath('message', 'AI chat is receiving requests too quickly. Please wait a moment and try again.');
    }

    public function test_ai_chat_works_in_local_demo_mode_without_openai_key(): void
    {
        config()->set('services.openai.api_key', null);
        config()->set('services.gemini.api_key', null);

        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $response = $this->actingAs($user)->postJson(route('ai-chat.store'), [
            'message' => 'Explain the registration flow.',
        ]);

        $response->assertOk();
        $response->assertJsonPath('model', 'local-demo');
        $this->assertStringContainsString('Luồng đăng ký', (string) $response->json('reply'));
    }

    public function test_html_form_submission_redirects_back_to_the_saved_conversation(): void
    {
        config()->set('services.openai.api_key', null);
        config()->set('services.gemini.api_key', null);

        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $response = $this->actingAs($user)->post(route('ai-chat.store'), [
            'message' => 'Explain the registration flow.',
        ]);

        $response->assertRedirect();
        $response->assertRedirectContains('/ai-chat?conversation=');

        $followUp = $this->actingAs($user)->followingRedirects()->post(route('ai-chat.store'), [
            'message' => 'Explain the registration flow.',
        ]);

        $followUp->assertOk();
        $followUp->assertSee('AI reply saved to the conversation.');
        $followUp->assertSee('SeminarBoost AI');
        $followUp->assertSee('Luồng đăng ký');
    }

    public function test_local_demo_mode_uses_the_project_knowledge_base(): void
    {
        config()->set('services.openai.api_key', null);
        config()->set('services.gemini.api_key', null);

        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $result = app(SeminarAiChat::class)->reply($user, 'Can you explain the project overview?');

        $this->assertSame('local-demo', $result['model']);
        $this->assertStringContainsString('Demo project là ứng dụng Laravel dùng để mô phỏng quy trình seminar trong trường đại học.', $result['reply']);
        $this->assertStringContainsString('React được dùng chủ yếu cho dashboard analytics và AI chat', $result['reply']);
    }

    public function test_local_demo_mode_answers_boost_specific_questions(): void
    {
        config()->set('services.openai.api_key', null);
        config()->set('services.gemini.api_key', null);

        $user = User::factory()->create([
            'role' => 'lecturer',
        ]);

        $result = app(SeminarAiChat::class)->reply($user, 'Laravel Boost là gì và nó giúp gì cho lập trình viên Laravel?');

        $this->assertSame('local-demo', $result['model']);
        $this->assertStringContainsString('Laravel Boost là lớp hỗ trợ AI dành cho Laravel', $result['reply']);
        $this->assertStringContainsString('AGENTS.md', $result['reply']);
    }

    public function test_gemini_mode_uses_gemini_api_when_key_is_present(): void
    {
        config()->set('services.gemini.api_key', 'test-gemini-key');
        config()->set('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
        config()->set('services.gemini.model', 'gemini-2.5-flash');

        Http::fake([
            'generativelanguage.googleapis.com/*:generateContent' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => '## Gemini demo reply'."\n".'- Laravel Boost là công cụ MCP server cho Laravel.'."\n\n".'Đây là câu trả lời từ Gemini.',
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $user = User::factory()->create([
            'role' => 'lecturer',
        ]);

        $result = app(SeminarAiChat::class)->reply($user, 'Laravel Boost là gì?');

        $this->assertSame('gemini-2.5-flash', $result['model']);
        $this->assertStringContainsString('Gemini demo reply', $result['reply']);
        $this->assertStringContainsString('Laravel Boost là công cụ MCP server cho Laravel', $result['reply']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'generativelanguage.googleapis.com')
                && $request['contents'][0]['parts'][0]['text'] !== '';
        });
    }
}
