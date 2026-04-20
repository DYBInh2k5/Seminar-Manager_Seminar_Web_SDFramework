# Deployment Guide

This document explains how to run and publish Seminar Manager in a practical environment.

## Local Development

### Requirements

- PHP 8.3
- Composer
- Node.js and npm
- SQLite or another supported database

### Install

```bash
composer install
npm install
```

### Environment setup

```bash
copy .env.example .env
php artisan key:generate
```

If you want to use the seeded local demo mode on this machine, keep the SQLite path in `.env` and make sure the database file exists.

### Database

```bash
php artisan migrate:fresh --seed
```

### Frontend assets

```bash
npm run dev
```

### Run the app

```bash
php artisan serve
```

Open:

- `http://127.0.0.1:8000`

## Production Checklist

Before deployment, review these items:

- set `APP_ENV=production`
- set `APP_DEBUG=false`
- use a real database instead of the temp SQLite demo file
- configure a production mail driver
- configure queue workers if you want background processing
- run `php artisan migrate --force`
- build frontend assets with `npm run build`
- create the storage symlink with `php artisan storage:link`

## Suggested Hosting Options

The project can be deployed on common PHP hosting or cloud platforms that support:

- Laravel 13
- PHP 8.3
- persistent storage
- a relational database

Suitable options include:

- shared hosting with Laravel support
- VPS or cloud server
- Laravel-friendly platforms such as Railway, Render, or Forge-managed servers

## AI Chat Environment Variables

If you want the cloud-backed AI assistant to work, configure:

```env
OPENAI_API_KEY=your_key_here
OPENAI_MODEL=gpt-4.1-mini
OPENAI_BASE_URL=https://api.openai.com/v1
```

If `OPENAI_API_KEY` is not set, Seminar Manager falls back to a local demo assistant so the chat still works for classroom demos.

## Common Troubleshooting

### Login page returns 500

- check that the SQLite file exists
- run migrations and seeders again
- make sure `SESSION_DRIVER=database` has a working database

### AI chat returns configuration error

- make sure `OPENAI_API_KEY` is set
- verify network access to the OpenAI API

### Frontend analytics or AI chat do not load

- run `npm install`
- run `npm run dev` during local development
- use `npm run build` for production

## Final Note

For this seminar project, the local demo setup is usually enough for classroom presentation, but the checklist above makes it ready to move toward production if needed.
