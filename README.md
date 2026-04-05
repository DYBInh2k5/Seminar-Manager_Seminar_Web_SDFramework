# Seminar Manager

A Laravel-based student seminar management system built for classroom seminar/demo use. The project showcases a complete seminar workflow with topic management, student registration, report submission, presentation scheduling, grading, analytics, and admin user management.

## Documentation

Detailed project documentation is available in these files:

- `PROJECT_OVERVIEW.md` - business context, goals, roles, and use cases
- `DATABASE.md` - database explanation, relationships, and ERD
- `ARCHITECTURE.md` - Laravel structure and application layers
- `API_FLOW.md` - route flow and database impact by feature
- `SEMINAR_SCRIPT.md` - ready-to-use seminar presentation script
- `README-DEMO.md` - quick demo guide

## Features

- Role-based authentication for `admin`, `lecturer`, and `student`
- Seminar topic CRUD
- Topic search and filtering
- Lecturer assignment for topics
- Student topic registration
- Report upload, download, replacement, and deletion
- Registration approval and rejection workflow
- Presentation scheduling
- Scoring and feedback
- Dashboard analytics
- AI chat assistant for seminar and project guidance
- Admin user management
- Printable topic summary page for browser PDF export
- Notification hooks using Laravel mail

## Tech Stack

- Laravel 13
- PHP 8.3
- Blade templates
- React for interactive dashboard analytics
- SQLite for local/demo usage
- PHPUnit feature tests

## Project Structure

- `app/Http/Controllers` - application controllers
- `app/Models` - Eloquent models
- `app/Support/SeminarNotifier.php` - lightweight notification service
- `app/Support/SeminarAiChat.php` - AI chat integration service
- `resources/views` - Blade UI
- `resources/js` - React-enhanced frontend modules
- `database/migrations` - schema definition
- `database/seeders` - demo data setup
- `tests/Feature` - project behavior tests
- `PROJECT_OVERVIEW.md` - project description and business context
- `DATABASE.md` - database explanation and relationship guide
- `ARCHITECTURE.md` - system architecture notes
- `API_FLOW.md` - route and request flow guide
- `SEMINAR_SCRIPT.md` - presentation speaking script

## Core Workflow

The main seminar flow in the system is:

1. Lecturer creates a topic.
2. Student registers for the topic.
3. Lecturer approves or rejects the registration.
4. Student uploads the seminar report.
5. Lecturer schedules the presentation.
6. Lecturer publishes the score and comment.

The central database table in this workflow is `registrations`, because it connects the student, topic, submission, presentation, and score records.

## Role Permissions

### Admin

- manage users
- access full dashboard data
- manage seminar records across the system

### Lecturer

- create and manage topics
- review registrations
- schedule presentations
- assign scores and comments

### Student

- browse topics
- register for topics
- upload reports
- review schedule and grading results

## Demo Accounts

Use these seeded accounts after running migrations and seeders:

- Admin: `admin@seminar.test` / `password`
- Lecturer: `lecturer@seminar.test` / `password`
- Student 1: `student1@seminar.test` / `password`
- Student 2: `student2@seminar.test` / `password`

## Local Setup

### 1. Install dependencies

```bash
composer install
```

### 2. Prepare environment

```bash
copy .env.example .env
php artisan key:generate
```

Add your AI credentials if you want to use the built-in assistant:

```bash
OPENAI_API_KEY=your_key_here
OPENAI_MODEL=gpt-4.1-mini
```

### 3. Run database setup

```bash
php artisan migrate:fresh --seed
```

### 4. Start the development server

```bash
npm install
npm run dev
php artisan serve
```

Open:

- `http://127.0.0.1:8000`

## Windows Notes

This project is configured to work around Windows path issues on this machine:

- SQLite is stored in a temp folder instead of the default project path
- Compiled Blade views are stored in a temp folder
- Frontend assets are optional at runtime, so the app still works in tests or before a Vite build finishes
- On some Windows setups, Vite/Tailwind native binaries may be sensitive to special characters in the folder path

If you move the project to another machine, review your `.env` settings for:

- `DB_DATABASE`
- `VIEW_COMPILED_PATH`

## Testing

Run the test suite with:

```bash
php artisan test
```

Current coverage includes:

- login page access
- dashboard access
- AI chat page and endpoint
- report upload/delete flow
- topic creation and filtering
- printable summary access
- admin user management access

## GitHub Preparation

This project is ready to be uploaded to GitHub with the current `.gitignore`.

Ignored by default:

- `.env`
- `vendor/`
- `node_modules/`
- `public/build`
- runtime storage files

Recommended GitHub workflow:

```bash
git init
git add .
git commit -m "Initial commit"
```

Then create a new repository on GitHub and connect it:

```bash
git remote add origin <your-repo-url>
git branch -M main
git push -u origin main
```

## Seminar Use Case

This project is suitable for:

- Laravel seminar presentations
- live feature demos
- coursework submission
- demonstrating how Laravel Boost can support real project development

## Suggested Repository Improvements

If you continue polishing the GitHub repository later, good next additions would be:

- screenshots or GIF previews
- deployment instructions
- future improvement roadmap
- contributor guide

## License

This project is open-sourced under the MIT license. See `LICENSE`.
