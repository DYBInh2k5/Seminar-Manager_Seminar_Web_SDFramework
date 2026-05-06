# Seminar Manager Documentation Index

This file is the entry point for the full documentation pack of the Seminar Manager project.

## How to Read the Docs

If you are a lecturer, reviewer, or classmate and want the fastest overview, follow this order:

1. [README.md](README.md)
2. [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md)
3. [DATABASE.md](DATABASE.md)
4. [ARCHITECTURE.md](ARCHITECTURE.md)
5. [API_FLOW.md](API_FLOW.md)
6. [SEMINAR_SCRIPT.md](SEMINAR_SCRIPT.md)
7. [LECTURER_PRESENTATION.md](LECTURER_PRESENTATION.md)
8. [README-DEMO.md](README-DEMO.md)
9. [DEPLOYMENT.md](DEPLOYMENT.md)
10. [AI_KNOWLEDGE_BASE.md](AI_KNOWLEDGE_BASE.md)

## File Map

### Project story

- `PROJECT_OVERVIEW.md`
- `README.md`

Use these files to explain:

- what the project is
- why it exists
- who uses it
- what problem it solves

### Database design

- `DATABASE.md`

Use this file to explain:

- table structure
- relationships
- ERD
- why `registrations` is the center of the workflow

### Architecture and code structure

- `ARCHITECTURE.md`

Use this file to explain:

- Laravel MVC layers
- support classes
- hybrid Blade + React frontend design
- request and rendering flow

### Route and request flow

- `API_FLOW.md`

Use this file to explain:

- which route triggers which action
- which tables are read or written
- how each role moves through the system

### Seminar speaking materials

- `SEMINAR_SCRIPT.md`
- `LECTURER_PRESENTATION.md`

Use these files to present:

- a student-style seminar script
- a lecturer-facing presentation outline
- quick answers for common questions

### Demo and verification

- `README-DEMO.md`
- `MANUAL_SMOKE_CHECKLIST.md`

Use these files to:

- run the app locally
- check the major user flows
- verify the project before presenting

### Deployment

- `DEPLOYMENT.md`

Use this file to explain:

- local setup
- production checklist
- AI environment variables
- common deployment issues

### AI assistant knowledge

- `AI_KNOWLEDGE_BASE.md`

Use this file to explain:

- how the chatbot is grounded in project data
- what the assistant knows
- why local demo mode still works without an OpenAI key

## Recommended Presentation Bundle

If you only need the minimum set for class, use these files:

- `README.md`
- `PROJECT_OVERVIEW.md`
- `DATABASE.md`
- `SEMINAR_SCRIPT.md`
- `README-DEMO.md`

If the lecturer wants more technical depth, also open:

- `ARCHITECTURE.md`
- `API_FLOW.md`
- `DEPLOYMENT.md`
- `AI_KNOWLEDGE_BASE.md`

## Project Status Summary

- Auth and role-based access are implemented
- Topic CRUD is implemented
- Registration, submissions, review, scheduling, and scoring are implemented
- Activity logs are implemented
- Dashboard analytics are implemented
- AI chat has saved conversations and knowledge-based replies
- Admin user management is implemented
- Tests cover the major flows

## One-Sentence Summary

Seminar Manager is a Laravel academic workflow app that manages seminar topics, registrations, reports, schedules, scores, analytics, and AI-assisted support in one place.
