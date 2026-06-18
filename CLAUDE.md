# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is **My Archive** — a Laravel 13.x web application built on PHP 8.3+. It's currently a fresh Laravel installation with PostgreSQL as the primary database, Tailwind CSS v4 for styling, Vite for frontend asset bundling, and Pest PHP for testing.

## Commands

```bash
# Full project setup (install deps, create .env, generate key, migrate, build assets)
composer run setup

# Start dev servers (PHP Artisan server, queue worker, logs, Vite — all concurrently)
composer run dev

# Run tests
composer run test

# Run tests with Pest directly
php artisan test

# Run a single test file
php artisan test --filter=ExampleTest

# Run Pest with a specific test
./vendor/bin/pest tests/Feature/ExampleTest.php

# Code style fix (Laravel Pint)
./vendor/bin/pint

# Run migrations
php artisan migrate

# Generate app key
php artisan key:generate
```

## Architecture

### Laravel 13.x Structure

- **Routes** — Defined in `routes/web.php` (web UI) and `routes/console.php` (Artisan commands). No API routes defined yet.
- **Controllers** — Located in `app/Http/Controllers/`. Currently only a base `Controller` class exists.
- **Models** — Located in `app/Models/`. Currently only the `User` model, which extends `Authenticatable` and uses `HasFactory` + `Notifiable` traits with PHP 8 attributes (`#[Fillable]`, `#[Hidden]`).
- **Views** — Blade templates in `resources/views/`. Currently only `welcome.blade.php` with Tailwind CSS.
- **Providers** — `AppServiceProvider` in `app/Providers/` with empty `register()` and `boot()` methods.
- **Config** — Standard Laravel config files in `config/`. Key overrides: database-backed sessions, cache, and queues.

### Database

- **Primary**: PostgreSQL (configured via `DB_CONNECTION=pgsql` in `.env`)
- **Connection details**: host 127.0.0.1:5432, database `my_archive`
- **Driver choices**: Sessions (database), Cache (database), Queue (database), Broadcast (log)
- **Migrations**: Standard Laravel migrations for users, password_reset_tokens, sessions, cache, jobs tables
- **Testing**: Uses SQLite in-memory database (configured in `phpunit.xml`)

### Frontend

- **Build tool**: Vite 8 with `laravel-vite-plugin` v3
- **CSS**: Tailwind CSS v4 with `@tailwindcss/vite` plugin
- **Font**: Instrument Sans (400, 500, 600 weights) via Bunny CDN
- **Entry points**: `resources/css/app.css`, `resources/js/app.js`

### Testing

- **Framework**: Pest PHP v4 with `pestphp/pest-plugin-laravel`
- **Test suites**: Unit (`tests/Unit/`) and Feature (`tests/Feature/`)
- **Base class**: `Tests\TestCase` (Laravel's base TestCase)
- Feature tests extend `TestCase` and run without `RefreshDatabase` by default (commented out in `tests/Pest.php`)

<!-- SPECKIT START -->
For additional context about the project architecture, data model, API
contracts, and implementation plan, read the feature plan:
specs/001-initial-project-spec/plan.md

Related design artifacts:
- Data model: specs/001-initial-project-spec/data-model.md
- API contracts: specs/001-initial-project-spec/contracts/
- Quickstart guide: specs/001-initial-project-spec/quickstart.md
- Research & decisions: specs/001-initial-project-spec/research.md
<!-- SPECKIT END -->
