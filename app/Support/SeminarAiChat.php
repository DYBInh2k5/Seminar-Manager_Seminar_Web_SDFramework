<?php

namespace App\Support;

use App\Models\Presentation;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class SeminarAiChat
{
    public function reply(User $user, string $message, ?string $previousResponseId = null): array
    {
        $apiKey = config('services.openai.api_key');

        if (! $apiKey) {
            return [
                'reply' => $this->localReply($user, $message),
                'response_id' => null,
                'model' => 'local-demo',
            ];
        }

        $payload = array_filter([
            'model' => config('services.openai.model'),
            'instructions' => $this->instructions($user),
            'input' => trim($message),
            'previous_response_id' => $previousResponseId,
        ]);

        try {
            $response = Http::baseUrl((string) config('services.openai.base_url'))
                ->withToken($apiKey)
                ->acceptJson()
                ->timeout(30)
                ->post('/responses', $payload)
                ->throw();
        } catch (RequestException $exception) {
            $apiMessage = data_get($exception->response?->json(), 'error.message');

            throw new RuntimeException($apiMessage ?: 'The AI provider could not process this request.');
        }

        $data = $response->json();

        return [
            'reply' => $this->extractReplyText($data),
            'response_id' => data_get($data, 'id'),
            'model' => data_get($data, 'model', config('services.openai.model')),
        ];
    }

    protected function instructions(User $user): string
    {
        $recentTopics = Topic::query()
            ->latest()
            ->take(5)
            ->pluck('title')
            ->implode(', ');

        $openTopics = Topic::query()->where('status', 'open')->count();
        $pendingRegistrations = Registration::query()->where('status', 'pending')->count();
        $roleContext = match ($user->role) {
            'student' => $this->studentContext($user),
            'lecturer' => $this->lecturerContext($user),
            'admin' => $this->adminContext(),
            default => 'No additional role-specific seminar context is available.',
        };

        return implode("\n", [
            'You are SeminarBoost AI, the built-in assistant for the Seminar Manager Laravel application.',
            'Your job is to help users understand seminar topics, registration flow, schedules, scoring, and how to use this system.',
            'Keep answers concise, practical, and easy for university users to follow.',
            'Prefer clean Markdown with short headings or bullet points when it improves clarity.',
            'If the user asks about project implementation, answer in terms of Laravel, Blade, React analytics, database tables, and role-based workflows.',
            'Do not invent private records or claim you changed data. You are a chat assistant, not an autonomous workflow executor.',
            "Current user role: {$user->role}.",
            "Current user name: {$user->name}.",
            "Open seminar topics in the system: {$openTopics}.",
            "Pending registrations in the system: {$pendingRegistrations}.",
            'Recent topic titles: ' . ($recentTopics !== '' ? $recentTopics : 'No topics available yet.'),
            $roleContext,
        ]);
    }

    protected function studentContext(User $user): string
    {
        $registrations = Registration::query()
            ->with(['topic', 'presentation', 'score', 'submission'])
            ->where('student_id', $user->id)
            ->latest()
            ->take(3)
            ->get()
            ->map(function (Registration $registration) {
                $presentation = $registration->presentation
                    ? $registration->presentation->scheduled_at->format('d/m/Y H:i') . ' in ' . $registration->presentation->room
                    : 'not scheduled';

                $score = $registration->score
                    ? number_format((float) $registration->score->score, 2) . '/10'
                    : 'not scored';

                $submission = $registration->submission
                    ? "{$registration->submission->review_status}, revision {$registration->submission->revision_number}"
                    : 'no report uploaded';

                return "{$registration->topic->title} ({$registration->status}, report: {$submission}, presentation: {$presentation}, score: {$score})";
            })
            ->implode('; ');

        return 'Student-specific context: ' . ($registrations !== '' ? $registrations : 'The student has no seminar registrations yet.');
    }

    protected function lecturerContext(User $user): string
    {
        $topicTitles = Topic::query()
            ->where('lecturer_id', $user->id)
            ->latest()
            ->take(5)
            ->pluck('title')
            ->implode(', ');

        $pendingReviews = Registration::query()
            ->where('status', 'pending')
            ->whereHas('topic', fn ($query) => $query->where('lecturer_id', $user->id))
            ->count();

        $submissionReviews = Submission::query()
            ->whereHas('registration.topic', fn ($query) => $query->where('lecturer_id', $user->id))
            ->whereIn('review_status', ['submitted', 'changes_requested'])
            ->count();

        return 'Lecturer-specific context: '
            . 'Topics supervised: ' . ($topicTitles !== '' ? $topicTitles : 'none yet') . '. '
            . "Pending registration approvals for this lecturer: {$pendingReviews}. "
            . "Submission reviews needing lecturer attention: {$submissionReviews}.";
    }

    protected function adminContext(): string
    {
        $upcomingPresentations = Presentation::query()
            ->where('scheduled_at', '>=', now())
            ->count();
        $acceptedReports = Submission::query()->where('review_status', 'accepted')->count();
        $changesRequested = Submission::query()->where('review_status', 'changes_requested')->count();

        return "Admin-specific context: upcoming presentations across the system: {$upcomingPresentations}. Accepted reports: {$acceptedReports}. Reports with requested changes: {$changesRequested}.";
    }

    protected function extractReplyText(array $data): string
    {
        $outputText = trim((string) data_get($data, 'output_text', ''));

        if ($outputText !== '') {
            return $outputText;
        }

        $segments = [];

        foreach ((array) data_get($data, 'output', []) as $item) {
            foreach ((array) data_get($item, 'content', []) as $content) {
                $text = data_get($content, 'text');

                if (is_string($text) && trim($text) !== '') {
                    $segments[] = trim($text);
                }
            }
        }

        $reply = trim(implode("\n\n", $segments));

        return $reply !== '' ? $reply : 'The AI assistant returned an empty response.';
    }

    protected function localReply(User $user, string $message): string
    {
        $lower = Str::of($message)->lower();

        if ($lower->contains(['database', 'schema', 'table'])) {
            return $this->localMarkdown(
                '## Database summary',
                [
                    '- The system centers on `registrations`.',
                    '- `submissions`, `presentations`, and `scores` all hang off a registration.',
                    '- `activity_logs` records important workflow actions.',
                ],
                'If you want, I can also explain the ERD in a simpler way.'
            );
        }

        if ($lower->contains(['registration', 'register'])) {
            return $this->localMarkdown(
                '## Registration flow',
                [
                    '- Student opens a topic page.',
                    '- Student clicks `Register` for an open topic.',
                    '- Lecturer reviews the request and approves or rejects it.',
                ],
                'This is the core flow used in the demo.'
            );
        }

        if ($lower->contains(['report', 'submission', 'review'])) {
            return $this->localMarkdown(
                '## Report review flow',
                [
                    '- Student uploads a report file.',
                    '- Lecturer can request changes or accept the submission.',
                    '- The student can resubmit a newer revision after feedback.',
                ],
                'The review note is stored directly on the submission record.'
            );
        }

        if ($lower->contains(['score', 'grading'])) {
            return $this->localMarkdown(
                '## Scoring flow',
                [
                    '- Lecturer enters a score between `0` and `10`.',
                    '- An optional comment is stored with the score.',
                    '- Students can see the result from the dashboard and topic page.',
                ],
                'This keeps grading connected to the registration workflow.'
            );
        }

        if ($lower->contains(['dashboard', 'analytics'])) {
            return $this->localMarkdown(
                '## Dashboard analytics',
                [
                    '- Laravel renders the main page.',
                    '- React renders the interactive analytics module.',
                    '- The dashboard shows status, role, department, and category breakdowns.',
                ],
                'The analytics layer is a good example of Blade plus React working together.'
            );
        }

        if ($lower->contains(['role', 'admin', 'lecturer', 'student'])) {
            return $this->localMarkdown(
                '## Role overview',
                [
                    '- `admin` manages users and oversees the whole system.',
                    '- `lecturer` manages topics, reviews reports, schedules presentations, and assigns scores.',
                    '- `student` registers, uploads reports, and tracks feedback.',
                ],
                'The permissions are kept simple so the workflow is easy to demonstrate.'
            );
        }

        return $this->localMarkdown(
            '## Seminar Manager',
            [
                '- This is the local demo assistant.',
                '- It can explain the project structure, workflow, database, and roles.',
                '- If you add `OPENAI_API_KEY`, it will switch to the OpenAI-powered assistant.',
            ],
            'Try asking about registrations, report review, scoring, dashboard analytics, or the database design.'
        );
    }

    protected function localMarkdown(string $title, array $bullets, string $closing): string
    {
        $lines = [$title];

        foreach ($bullets as $bullet) {
            $lines[] = $bullet;
        }

        $lines[] = '';
        $lines[] = $closing;

        return implode("\n", $lines);
    }
}
