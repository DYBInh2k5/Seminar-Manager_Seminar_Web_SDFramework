<?php

namespace App\Support;

use Illuminate\Support\Str;

class SeminarKnowledgeBase
{
    public static function contextBlock(): string
    {
        return implode("\n", [
            'Project knowledge base:',
            '- Seminar Manager is a Laravel-based academic workflow app for university seminars.',
            '- The frontend is a hybrid of Blade and React, with React used mainly for dashboard analytics and AI chat.',
            '- The app supports three main roles: admin, lecturer, and student.',
            '- The core entity is `registrations`, which links students to topics and drives submissions, presentations, and scores.',
            '- The system includes topic management, registration approvals, report review, presentation scheduling, scoring, activity logs, and AI chat.',
            '- The chatbot should answer in practical terms and avoid inventing records that are not in the database.',
        ]);
    }

    public static function answerFor(string $message, string $role): ?array
    {
        $lower = Str::of($message)->lower();

        $topics = [
            [
                'keywords' => ['overview', 'project', 'seminar manager', 'what is this'],
                'title' => 'Project overview',
                'bullets' => [
                    '- Seminar Manager is a Laravel-based academic workflow app for university seminars.',
                    '- It helps universities organize seminar topics, registrations, report submissions, presentations, and scores.',
                    '- React is used mainly for dashboard analytics and AI chat.',
                    '- It is built with Laravel and enhanced with React for interactive dashboard analytics and AI chat.',
                    '- The app is designed to be easy to demo in class and realistic enough to explain an academic workflow.',
                ],
                'closing' => 'If you want, I can also explain the database or the role permissions.',
            ],
            [
                'keywords' => ['database', 'schema', 'table', 'erd'],
                'title' => 'Database knowledge',
                'bullets' => [
                    '- `users` stores admins, lecturers, and students.',
                    '- `topics` stores seminar topics, including capacity, semester, category, and difficulty.',
                    '- `registrations` is the central table that connects a student to a topic.',
                    '- `submissions`, `presentations`, `scores`, and `activity_logs` all hang off the registration flow.',
                ],
                'closing' => 'I can also walk through the table relationships in a simple step-by-step way.',
            ],
            [
                'keywords' => ['role', 'admin', 'lecturer', 'student', 'permissions'],
                'title' => 'Role permissions',
                'bullets' => [
                    '- Admin manages users, topics, and overall system visibility.',
                    '- Lecturer manages seminar topics, approves registrations, reviews reports, schedules presentations, and publishes scores.',
                    '- Student registers for topics, uploads reports, follows feedback, and checks results.',
                ],
                'closing' => 'The permissions are intentionally simple so the seminar demo stays clear.',
            ],
            [
                'keywords' => ['registration', 'register', 'topic signup'],
                'title' => 'Registration flow',
                'bullets' => [
                    '- Student opens a topic detail page.',
                    '- Student clicks register if the topic is open and has capacity.',
                    '- Lecturer reviews the request and approves or rejects it.',
                    '- Once approved, the registration becomes part of the seminar workflow.',
                ],
                'closing' => 'This is the main workflow the demo follows from start to finish.',
            ],
            [
                'keywords' => ['report', 'submission', 'review', 'resubmit', 'revision'],
                'title' => 'Report review flow',
                'bullets' => [
                    '- Student uploads a PDF, DOC, or DOCX report.',
                    '- Lecturer can accept the submission or request changes with a review note.',
                    '- If changes are requested, the student can resubmit a newer revision.',
                    '- Each revision is tracked so the history is easy to follow.',
                ],
                'closing' => 'That gives the project a more realistic academic feedback loop.',
            ],
            [
                'keywords' => ['score', 'grading', 'grade', 'marks'],
                'title' => 'Scoring flow',
                'bullets' => [
                    '- Lecturer enters a score from 0 to 10.',
                    '- A comment can be stored with the score.',
                    '- The result is visible to the student from the dashboard and topic detail page.',
                ],
                'closing' => 'The score is tied to the registration, so it stays connected to the student workflow.',
            ],
            [
                'keywords' => ['dashboard', 'analytics', 'chart', 'stats'],
                'title' => 'Dashboard analytics',
                'bullets' => [
                    '- Laravel renders the main app shell and data.',
                    '- React renders the interactive analytics panel.',
                    '- The dashboard can show registration status, role distribution, department breakdown, and topic category insights.',
                ],
                'closing' => 'This is the best place to show the Laravel plus React hybrid design.',
            ],
            [
                'keywords' => ['ai chat', 'chatbot', 'assistant', 'openai', 'gpt'],
                'title' => 'AI chat assistant',
                'bullets' => [
                    '- The AI chat can run in OpenAI mode when an API key is configured.',
                    '- It also has a local demo mode so the app still works without external access.',
                    '- Saved conversations belong to each user, so the assistant can keep context.',
                ],
                'closing' => 'This makes the demo stable for class presentations and offline testing.',
            ],
            [
                'keywords' => ['deployment', 'run', 'install', 'setup'],
                'title' => 'Run and deploy',
                'bullets' => [
                    '- Install Composer dependencies and NPM dependencies.',
                    '- Run migrations and seed the demo data.',
                    '- Start Laravel and Vite for the hybrid UI.',
                ],
                'closing' => 'I can also give you a quick demo checklist if you need it.',
            ],
        ];

        foreach ($topics as $topic) {
            foreach ($topic['keywords'] as $keyword) {
                if ($lower->contains($keyword)) {
                    return [
                        'title' => $topic['title'],
                        'bullets' => $topic['bullets'],
                        'closing' => $topic['closing'],
                    ];
                }
            }
        }

        $roleTopic = match ($role) {
            'student' => [
                'title' => 'Student guidance',
                'bullets' => [
                    '- You can ask about registration, reports, feedback, presentations, and scores.',
                    '- The assistant can summarize your current workflow in simple terms.',
                ],
                'closing' => 'Try asking: "What should I do after I register for a topic?"',
            ],
            'lecturer' => [
                'title' => 'Lecturer guidance',
                'bullets' => [
                    '- You can ask about approvals, report review, presentation planning, and grading.',
                    '- The assistant can explain how lecturer actions affect the workflow.',
                ],
                'closing' => 'Try asking: "Show me the lecturer workflow for seminar approval."',
            ],
            'admin' => [
                'title' => 'Admin guidance',
                'bullets' => [
                    '- You can ask about users, analytics, topic management, and system overview.',
                    '- The assistant can explain how the admin role supports the full system.',
                ],
                'closing' => 'Try asking: "Summarize the whole project structure for me."',
            ],
            default => null,
        };

        return $roleTopic;
    }
}
