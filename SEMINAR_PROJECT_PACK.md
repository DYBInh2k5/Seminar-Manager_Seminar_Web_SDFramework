# Seminar Manager Project Pack

This document is a compact but detailed pack you can use for seminar submission, preparation, or quick revision.

## 1. Project Name

**Seminar Manager**

## 2. Project Type

A Laravel-based academic seminar management system.

## 3. Main Goal

To centralize the entire seminar lifecycle in one web application, from topic creation and student registration to report review, presentation scheduling, and final scoring.

## 4. Problem It Solves

In many academic environments, seminar tasks are scattered across spreadsheets, chat messages, and separate files. That makes the process hard to track.

This project solves the problem by organizing the workflow into a single system with clear roles and a relational database.

## 5. Main Roles

### Admin

- manage users
- view all seminar data
- oversee the whole system

### Lecturer

- create and manage seminar topics
- approve or reject registrations
- review reports and request changes
- schedule presentations
- publish scores

### Student

- browse topics
- register for a topic
- upload and resubmit reports
- check schedules and scores

## 6. Core Features

- authentication and role control
- topic CRUD
- search and filter
- registration approval flow
- report upload and review
- resubmission support
- presentation scheduling
- scoring and comments
- dashboard analytics
- activity logs
- admin user management
- printable topic summary
- AI chat assistant

## 7. Data Model Highlights

The most important table is `registrations`.

It links:

- one student
- one topic

Then other tables attach to that registration:

- `submissions`
- `presentations`
- `scores`

The project also includes:

- `users`
- `topics`
- `ai_chat_conversations`
- `ai_chat_messages`
- `activity_logs`

## 8. Technology Stack

- Laravel 13
- PHP 8.3
- Blade
- React for dashboard analytics and AI chat UI
- SQLite for local demo
- PHPUnit for tests

## 9. Architecture Summary

The app uses a hybrid approach:

- Blade renders the main pages
- React enhances interactive parts
- Controllers manage workflow and authorization
- Eloquent models represent the database
- migrations define schema
- seeders populate demo data

## 10. AI Assistant

The chatbot can:

- answer about the project
- explain the seminar workflow
- explain the database
- answer by role

It works in two modes:

- local demo mode with a curated knowledge base
- OpenAI mode when `OPENAI_API_KEY` is configured

## 11. Demo Flow

Recommended live demo:

1. Lecturer logs in
2. Lecturer creates a topic
3. Student logs in
4. Student registers for the topic
5. Student uploads a report
6. Lecturer reviews it
7. Lecturer schedules the presentation
8. Lecturer enters a score
9. Open dashboard analytics
10. Open AI chat

## 12. Testing Status

The project includes feature tests for:

- login
- dashboard
- topic management
- registrations
- submissions
- user management
- AI chat

## 13. Why This Project Is Good for a Seminar

It is a strong seminar project because it combines:

- a real academic use case
- a relational database
- role-based workflow
- file handling
- analytics
- AI support
- clean presentation material

## 14. Short Seminar Summary

Seminar Manager is a Laravel application for managing the academic seminar lifecycle from topic creation to final grading.
