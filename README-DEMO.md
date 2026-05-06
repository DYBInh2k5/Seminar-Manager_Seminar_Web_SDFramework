# Seminar Manager Demo

## Implemented features
- Role-based login for admin, lecturer, and student
- Dashboard overview with React-powered analytics and lecturer leaderboard
- AI chat assistant for project and seminar guidance
- Seminar topic CRUD
- Topic search and filtering
- Admin lecturer assignment for topics
- Student topic registration
- Student report upload, replacement, download, and deletion
- Lecturer approval and rejection workflow
- Presentation scheduling for approved registrations
- Scoring and feedback
- Admin user management
- Printable topic summary page for browser PDF export
- Email notifications via the configured Laravel mail driver

## Demo accounts
- Admin: `admin@seminar.test` / `password`
- Lecturer: `lecturer@seminar.test` / `password`
- Student 1: `student1@seminar.test` / `password`
- Student 2: `student2@seminar.test` / `password`

## Run the project
```bash
cd seminar-manager
npm install
npm run dev
php artisan serve
```

Open your browser at:
- `http://127.0.0.1:8000`

## Environment note for this machine
This project currently uses:
- SQLite in the temp folder to avoid Windows path issues
- Blade compiled views in the temp folder to avoid view compilation issues
- Blade for the main UI and React for the dashboard analytics module
- The default Laravel `log` mail driver, so outgoing mail is written to logs rather than sent externally

If the frontend assets are not running yet, the application still works because React is used as an enhancement layer instead of a hard requirement for the full UI.

To use the cloud AI assistant, add `OPENAI_API_KEY` in your `.env` file.
If you do not set it, the chat still works in local demo mode with a curated project knowledge base.

The knowledge base is documented in:
- `AI_KNOWLEDGE_BASE.md`

If you need to reset the demo data:
```bash
php artisan migrate:fresh --seed
```

## Current verification status
- `php artisan test` passes
- `php artisan migrate:fresh --seed` passes

## Manual smoke checklist

If you want a step-by-step demo verification list, open:

- `MANUAL_SMOKE_CHECKLIST.md`

For the full documentation hub, open:

- `DOCUMENTATION_INDEX.md`
- `SEMINAR_PROJECT_PACK.md`
