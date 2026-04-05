# API and Route Flow

This project is primarily a server-rendered Laravel web application, so it does not expose a separate REST API for frontend JavaScript clients. Instead, the main business flow happens through Laravel web routes.

The current React usage is limited to the dashboard analytics section, and the page receives that data directly from the Laravel view rather than from a dedicated API endpoint.

This file explains which route performs which action and which database tables are affected.

## Route Groups

### Guest routes

Accessible before login:

- `GET /login`
- `POST /login`

Purpose:

- show login page
- authenticate user

Main controller:

- `AuthController`

Affected tables:

- `users`
- `sessions`

### Authenticated routes

Accessible only after login:

- dashboard
- ai chat
- topics
- registrations
- submissions
- presentations
- scores
- user management

## Route-by-Route Flow

### Dashboard

Route:

- `GET /dashboard`

Controller:

- `DashboardController`

Purpose:

- show overall system statistics and user-specific information
- provide structured analytics data to the React dashboard module

Reads from:

- `users`
- `topics`
- `registrations`
- `submissions`
- `presentations`
- `scores`

Writes to:

- none

Frontend note:

- this route still returns a Blade page
- React mounts inside the dashboard page for analytics only

### AI chat

Routes:

- `GET /ai-chat`
- `POST /ai-chat`
- `POST /ai-chat/conversations`
- `GET /ai-chat/conversations/{conversation}`

Controller:

- `AiChatController`

Purpose:

- render the AI chat page
- create saved conversations
- send user messages to the AI provider
- reopen previous conversations

Reads from:

- `users`
- `topics`
- `registrations`
- `presentations`
- `scores`
- `ai_chat_conversations`
- `ai_chat_messages`

Writes to:

- `ai_chat_conversations`
- `ai_chat_messages`

Frontend note:

- Blade renders the page shell
- React manages the chat interface and conversation history

### Topics

Routes:

- `GET /topics`
- `GET /topics/{topic}`
- `GET /topics/create`
- `POST /topics`
- `GET /topics/{topic}/edit`
- `PUT /topics/{topic}`
- `DELETE /topics/{topic}`

Controller:

- `TopicController`

Purpose:

- browse topics
- search and filter topics
- create and update topic information
- assign lecturer ownership

Reads from:

- `topics`
- `users`
- `registrations`

Writes to:

- `topics`

### Printable topic summary

Route:

- `GET /topics/{topic}/summary`

Controller:

- `ExportController`

Purpose:

- show a print-friendly topic summary page

Reads from:

- `topics`
- `registrations`
- `submissions`
- `presentations`
- `scores`
- `users`

Writes to:

- none

### Registrations

Routes:

- `POST /topics/{topic}/register`
- `PATCH /registrations/{registration}/status`

Controller:

- `RegistrationController`

Purpose:

- create a student registration
- approve or reject a registration

Reads from:

- `topics`
- `users`
- `registrations`

Writes to:

- `registrations`

Typical status transitions:

- `pending` -> `approved`
- `pending` -> `rejected`

### Submissions

Routes:

- `POST /registrations/{registration}/submission`
- `DELETE /submissions/{submission}`
- `GET /submissions/{submission}/download`

Controller:

- `SubmissionController`

Purpose:

- upload seminar report
- delete seminar report
- download seminar report

Reads from:

- `registrations`
- `submissions`

Writes to:

- `submissions`
- file storage

### Presentations

Routes:

- `GET /registrations/{registration}/presentation/create`
- `POST /registrations/{registration}/presentation`
- `GET /presentations/{presentation}/edit`
- `PUT /presentations/{presentation}`

Controller:

- `PresentationController`

Purpose:

- create or edit a seminar schedule

Reads from:

- `registrations`
- `presentations`

Writes to:

- `presentations`

### Scores

Routes:

- `POST /registrations/{registration}/score`
- `PUT /scores/{score}`

Controller:

- `ScoreController`

Purpose:

- create or update final seminar score and comment

Reads from:

- `registrations`
- `scores`

Writes to:

- `scores`

### User management

Routes:

- `GET /users`
- `GET /users/create`
- `POST /users`
- `GET /users/{user}/edit`
- `PUT /users/{user}`
- `DELETE /users/{user}`

Controller:

- `UserManagementController`

Purpose:

- admin-only user management

Reads from:

- `users`

Writes to:

- `users`

## Flow Mapping by Use Case

### Student journey

1. `GET /login`
2. `POST /login`
3. `GET /topics`
4. `GET /topics/{topic}`
5. `POST /topics/{topic}/register`
6. `POST /registrations/{registration}/submission`
7. `GET /dashboard`

Tables touched:

- `users`
- `sessions`
- `topics`
- `registrations`
- `submissions`

### Lecturer journey

1. `POST /login`
2. `GET /topics/create`
3. `POST /topics`
4. `PATCH /registrations/{registration}/status`
5. `POST /registrations/{registration}/presentation`
6. `POST /registrations/{registration}/score`
7. `GET /topics/{topic}/summary`

Tables touched:

- `users`
- `sessions`
- `topics`
- `registrations`
- `presentations`
- `scores`

### Admin journey

1. `POST /login`
2. `GET /dashboard`
3. `GET /users`
4. `POST /users`
5. `PUT /users/{user}`
6. `DELETE /users/{user}`

Tables touched:

- `users`
- `sessions`
- dashboard read queries across seminar tables

## Route Security Summary

### Public

- login only

### Student-only actions

- register for a topic
- upload a submission

### Lecturer and admin actions

- create and edit topics
- approve registrations
- schedule presentations
- assign scores
- open printable summary

### Admin-only actions

- manage users

## Simple Request Lifecycle

```text
Browser -> web route -> middleware -> controller -> model/database -> redirect/view
```

This is the main request pattern across the project.
