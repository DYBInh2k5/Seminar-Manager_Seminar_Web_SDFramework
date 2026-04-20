# Manual Smoke Checklist

Use this checklist to verify the Seminar Manager project manually before a demo or submission.

## Environment

- `php artisan migrate:fresh --seed` completed successfully
- `php artisan serve` is running
- `npm run dev` is running
- Login page opens at `http://127.0.0.1:8000/login`

## Login and Roles

- Login as admin works
- Login as lecturer works
- Login as student works
- Invalid credentials show a validation or login error

## Dashboard

- Dashboard opens after login
- Summary cards display topic and registration counts
- React analytics section renders
- Role breakdown appears
- Department breakdown appears
- Category breakdown appears
- Top lecturers leaderboard appears for admin and lecturer users

## Topic Management

- Lecturer or admin can create a topic
- Topic form accepts category, capacity, semester, difficulty, and expected outcomes
- Topic list can be searched by title or description
- Topic list can be filtered by status
- Topic list can be filtered by category
- Topic list can be filtered by difficulty
- Admin can assign a lecturer
- Topic detail page opens correctly
- Printable summary page opens correctly

## Registration Flow

- Student can register for an open topic
- Duplicate registration is blocked
- Registration is blocked when topic capacity is full
- Lecturer can approve a pending registration
- Lecturer can reject a pending registration

## Submission Flow

- Student can upload a PDF report
- Student can upload DOC or DOCX reports
- Student can see the uploaded report on dashboard and topic detail
- Student can delete their report
- Lecturer can review a report
- Lecturer can request changes
- Lecturer can accept a report
- Student can resubmit after a change request
- Revision number increases after resubmission
- Review note is visible to the student

## Presentation and Scoring

- Lecturer can create a presentation schedule
- Lecturer can edit a presentation schedule
- Lecturer can save a score
- Lecturer can update a score
- Student can see the schedule and score

## Activity Logs

- Activity page opens
- Topic creation appears in activity feed
- Registration approval appears in activity feed
- Report review appears in activity feed
- Presentation scheduling appears in activity feed
- Score publishing appears in activity feed

## AI Chat

- AI Chat page opens
- Saved conversations load correctly
- New conversation can be created
- Quick actions appear
- Quick actions send a prompt successfully
- Manual message can be sent successfully
- Rate limit returns a friendly error after repeated requests
- Local demo mode works when `OPENAI_API_KEY` is empty
- Cloud AI mode works when `OPENAI_API_KEY` is configured

## Admin User Management

- Admin can open the user management page
- Admin can create a user
- Admin can edit a user
- Admin can delete a user
- Non-admin users are blocked from user management

## Notifications and Demo Data

- Demo seed data creates multiple lecturers
- Demo seed data creates multiple students
- Demo seed data creates topics with different categories
- Demo seed data creates open, pending, approved, rejected, and closed examples
- Email notifications are written to the Laravel log driver during demo mode

## Verification Summary

- Automated tests pass
- Key flows are visible in the UI
- The project is ready for live seminar presentation
