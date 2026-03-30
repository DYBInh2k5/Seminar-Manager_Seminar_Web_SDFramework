# Project Overview

## Introduction

Seminar Manager is a Laravel-based web application for managing the full seminar lifecycle in an academic environment. It was built as a classroom seminar project and is suitable for live demos, coursework submission, and discussion of Laravel development practices.

The system supports three main user roles:

- `admin`
- `lecturer`
- `student`

Each role interacts with the system differently, but all of them participate in one connected business workflow.

## Problem Statement

In many classroom seminar settings, the workflow is managed manually through spreadsheets, chat messages, and separate files. That creates several problems:

- topic information is scattered
- student registrations are hard to track
- approval decisions are not centralized
- report files are difficult to manage
- presentation schedules can be missed
- grading data may be inconsistent

Seminar Manager solves this by keeping the entire seminar process in one Laravel application.

## Project Goal

The goal of the project is to provide a clear and realistic seminar management workflow that demonstrates:

- role-based access control
- relational database design
- Laravel CRUD patterns
- file upload handling
- approval workflow design
- scheduling and grading flows
- dashboard analytics
- testable application structure

## Target Users

### Admin

Admin users oversee the entire system.

Responsibilities:

- manage users
- access all seminar data
- support lecturers and students
- review overall dashboard metrics

### Lecturer

Lecturers are responsible for seminar supervision.

Responsibilities:

- create and update seminar topics
- review student registrations
- approve or reject requests
- schedule presentations
- assign scores and comments

### Student

Students participate in seminar topics.

Responsibilities:

- browse available topics
- register for a topic
- upload a report file
- track approval status
- check presentation schedule
- view score and feedback

## Core Features

### Authentication and Authorization

- login system for all users
- role-based access control for pages and actions

### Topic Management

- create seminar topics
- edit seminar topics
- delete seminar topics
- filter and search topics
- assign a lecturer to a topic

### Registration Management

- students register for available topics
- duplicate topic registration is prevented per student
- lecturers approve or reject registrations

### Report Submission

- students upload seminar reports
- supported file lifecycle includes upload, download, and deletion
- each registration has at most one active submission

### Presentation Scheduling

- lecturers schedule a presentation for a registration
- room and date/time are stored

### Scoring and Feedback

- lecturers enter a final score
- optional text feedback can be stored with the score

### Dashboard and Analytics

- summary cards and reporting information
- role breakdown
- registration status breakdown
- lecturer activity summaries

### Admin User Management

- admins create users
- admins edit user information
- admins manage user roles

### Export Support

- printable topic summary for browser print or PDF export

## End-to-End Workflow

1. Lecturer creates a topic.
2. Student browses open topics.
3. Student registers for a topic.
4. Registration is stored as `pending`.
5. Lecturer reviews the registration.
6. Lecturer changes status to `approved` or `rejected`.
7. Student uploads a seminar report.
8. Lecturer schedules the presentation.
9. Lecturer publishes score and feedback.
10. Admin can monitor the whole process through the dashboard and user management pages.

## Technical Summary

- Framework: Laravel 13
- Language: PHP 8.3
- Frontend approach: Laravel Blade with a React-powered analytics module
- Database: SQLite for local/demo setup
- Test framework: PHPUnit feature tests

## Frontend Strategy

The project uses a hybrid frontend approach instead of a full React SPA.

Current design:

- Laravel Blade renders the main pages, forms, and workflow screens
- React enhances the dashboard analytics area with more interactive behavior
- Vite is used to build frontend assets when JavaScript is enabled

Why this is a good fit:

- it keeps the Laravel workflow easy to explain in a seminar
- it avoids rewriting the whole system into a separate frontend
- it still demonstrates that the project can integrate React in a practical way
- it provides a realistic example of progressive enhancement

## Why This Project Works Well for a Seminar

This project is a strong seminar/demo project because it contains:

- clear real-world entities
- many relational database examples
- multiple user roles
- a visible business workflow
- common Laravel features in one system

It is also small enough to explain in a presentation, but complete enough to show meaningful functionality.

## Suggested Demo Story

If you want to present the system in class, this is the simplest story:

1. Log in as lecturer and create a topic.
2. Log in as student and register for that topic.
3. Upload a report file.
4. Log in as lecturer and approve the registration.
5. Schedule the presentation.
6. Add a score and comment.
7. Show the dashboard and printable summary.

That flow demonstrates nearly every important table and feature in the project.

If you want to specifically mention React during the demo, open the dashboard and explain that the analytics module is rendered with React while the rest of the system remains server-rendered with Laravel.
