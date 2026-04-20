# AI Knowledge Base

This file documents the curated knowledge base used by the Seminar Manager AI assistant.

## Purpose

The project does not fine-tune a model locally. Instead, it uses a curated knowledge base to ground the chatbot in the real structure of the application.

This makes the AI assistant:

- more accurate for seminar-related questions
- safer in local demo mode
- easier to maintain and expand

## Core Facts

- Seminar Manager is a Laravel-based academic workflow app.
- The frontend uses a hybrid of Blade and React.
- React is used mainly for dashboard analytics and the AI chat interface.
- The system has three roles: `admin`, `lecturer`, and `student`.
- The central table is `registrations`.
- Related workflow tables include `submissions`, `presentations`, `scores`, and `activity_logs`.

## Main Questions the AI Can Answer

- What is the project about?
- How does the registration flow work?
- How are reports reviewed?
- How are scores published?
- What does each role do?
- How does the dashboard use React?
- What does the database look like?

## Local Demo Behavior

When `OPENAI_API_KEY` is not configured, the assistant uses the local knowledge base and returns markdown replies based on the project facts.

When `OPENAI_API_KEY` is configured, the same project knowledge block is added to the OpenAI prompt so the assistant stays grounded in the actual app.

## Notes

This knowledge base is intentionally small and curated.
It is not a machine learning training pipeline.
It is a practical project context layer for a university seminar app.
