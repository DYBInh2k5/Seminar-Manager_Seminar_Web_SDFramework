# Seminar Manager Demo

## Implemented features
- Role-based login for admin, lecturer, and student
- Dashboard overview with analytics cards and lecturer leaderboard
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
php artisan serve
```

Open your browser at:
- `http://127.0.0.1:8000`

## Environment note for this machine
This project currently uses:
- SQLite in the temp folder to avoid Windows path issues
- Blade compiled views in the temp folder to avoid view compilation issues
- The default Laravel `log` mail driver, so outgoing mail is written to logs rather than sent externally

If you need to reset the demo data:
```bash
php artisan migrate:fresh --seed
```

## Current verification status
- `php artisan test` passes
- `php artisan migrate:fresh --seed` passes
