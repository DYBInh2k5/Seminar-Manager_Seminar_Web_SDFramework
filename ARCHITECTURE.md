# Architecture Guide

## High-Level Structure

The project follows a standard Laravel MVC structure:

- `Models` represent the database entities
- `Controllers` handle request logic
- `Views` render the user interface with Blade templates
- `Routes` map URLs to controller actions
- `Migrations` define the database schema
- `Seeders` provide demo data

On top of that Laravel structure, the dashboard includes a focused React module for interactive analytics.

## Application Layers

### Presentation Layer

Main location:

- `resources/views`
- `resources/js`

Purpose:

- render login pages
- render dashboards
- render topic, user, and scheduling forms
- show topic details, report status, and grading information

Frontend strategy:

- Blade handles the main page layout and most user interface screens
- React mounts only into the dashboard analytics section
- Vite bundles the JavaScript entry point when frontend assets are running

Main layout:

- `resources/views/layouts/app.blade.php`

Main React files:

- `resources/js/app.js`
- `resources/js/components/DashboardAnalytics.jsx`

### Routing Layer

Main location:

- `routes/web.php`

Purpose:

- define all web routes
- apply authentication middleware
- apply role-based middleware
- connect routes to controllers

Examples:

- topic CRUD routes
- registration routes
- submission routes
- presentation routes
- score routes
- user management routes

### Controller Layer

Main location:

- `app/Http/Controllers`

Responsibilities:

- validate input
- authorize actions
- fetch related models
- update records
- redirect back with messages

Main controllers:

- `AuthController`
- `AiChatController`
- `DashboardController`
- `TopicController`
- `RegistrationController`
- `SubmissionController`
- `PresentationController`
- `ScoreController`
- `UserManagementController`
- `ExportController`

### Domain Layer

Main location:

- `app/Models`

Purpose:

- define entity relationships
- expose Eloquent model behavior
- support query loading in controllers and views

Main models:

- `AiChatConversation`
- `AiChatMessage`
- `User`
- `Topic`
- `Registration`
- `Submission`
- `Presentation`
- `Score`

### Support Layer

Main location:

- `app/Support`

Current support class:

- `SeminarNotifier`
- `SeminarAiChat`

Purpose:

- centralize lightweight notification behavior
- keep controller code cleaner
- integrate seminar-aware AI responses through an external provider

### Persistence Layer

Main location:

- `database/migrations`
- `database/seeders`

Purpose:

- define schema
- define foreign keys and unique constraints
- seed demo accounts and sample data

## Request Flow Example

### Student registration flow

1. The user opens a topic page.
2. A POST request is sent to the registration route.
3. `RegistrationController@store` validates the action.
4. A `registrations` record is created.
5. The user is redirected with a status message.

### Report submission flow

1. A student uploads a file from the dashboard or topic page.
2. The request reaches `SubmissionController@store`.
3. The file is validated and stored.
4. The `submissions` table is updated.
5. A notification hook may run.
6. The page redirects back with updated status.

### Scoring flow

1. A lecturer submits a score form.
2. The request reaches `ScoreController`.
3. The controller validates score and comment.
4. The `scores` table is created or updated.
5. The student can then see the result in the UI.

### AI chat flow

1. A user opens the AI chat page.
2. React loads recent conversations from Blade bootstrap data.
3. The user sends a message.
4. `AiChatController@store` saves the user message.
5. `SeminarAiChat` builds project and seminar context, then calls the AI provider.
6. The assistant reply is saved to the same conversation.
7. The user can reopen the conversation later.

## Authorization Model

The project uses role-based access control.

Role examples:

- `admin` can manage users and system-wide data
- `lecturer` can manage topics and academic decisions
- `student` can register and submit reports

Middleware:

- authentication middleware protects private routes
- custom role middleware limits actions to allowed roles

Main middleware file:

- `app/Http/Middleware/EnsureUserHasRole.php`

## Data Design Principle

The architecture treats `registrations` as the process center of the app.

Why:

- it links a student to a topic
- it stores approval state
- it connects submission, presentation, and score records

Because of that, most academic process actions are built around one registration record.

## UI Design Principle

The UI is built for simple demonstration and classroom use:

- straightforward navigation
- role-aware actions
- dashboard summaries
- readable forms and tables

This is not a complex design-system-heavy frontend. It is intentionally practical and easy to present.

The React integration is intentionally small and purposeful:

- it improves dashboard interactivity
- it keeps the rest of the project easy to follow
- it shows that Laravel can work with React without becoming a full SPA

## Testing Strategy

Current test coverage focuses on feature behavior instead of low-level unit logic.

Included areas:

- page access
- AI chat behavior and access control
- topic creation and filtering
- submission workflow
- user management behavior

This is appropriate for a CRUD-heavy Laravel project where user flows matter more than isolated utility functions.

## Suggested Reading Order

If someone wants to understand the project quickly, this is the best order:

1. `README.md`
2. `DATABASE.md`
3. `routes/web.php`
4. `app/Models`
5. `app/Http/Controllers`
6. `resources/views`
7. `resources/js`

That order moves from high-level overview to implementation detail.
