# Seminar Manager

A Laravel-based student seminar management system built for classroom seminar/demo use. The project showcases a complete seminar workflow with topic management, student registration, report submission, presentation scheduling, grading, analytics, and admin user management.

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
- Admin user management
- Printable topic summary page for browser PDF export
- Notification hooks using Laravel mail

## Tech Stack

- Laravel 13
- PHP 8.3
- Blade templates
- SQLite for local/demo usage
- PHPUnit feature tests

## Project Structure

- `app/Http/Controllers` - application controllers
- `app/Models` - Eloquent models
- `app/Support/SeminarNotifier.php` - lightweight notification service
- `resources/views` - Blade UI
- `database/migrations` - schema definition
- `database/seeders` - demo data setup
- `tests/Feature` - project behavior tests

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

### 3. Run database setup

```bash
php artisan migrate:fresh --seed
```

### 4. Start the development server

```bash
php artisan serve
```

Open:

- `http://127.0.0.1:8000`

## Windows Notes

This project is configured to work around Windows path issues on this machine:

- SQLite is stored in a temp folder instead of the default project path
- Compiled Blade views are stored in a temp folder

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

## License

This project is open-sourced under the MIT license.
