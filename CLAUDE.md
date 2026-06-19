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

### Domain-Based Modular Architecture

The project uses a domain-based modular architecture under `Modules/` instead of the standard
Laravel `app/` structure. Each domain module is self-contained with its own classes, views,
routes, and tests.

```text
Modules/
├── Core/              Shared infrastructure (traits, contracts, enums, exceptions, helpers,
│                      providers, support classes, shared Livewire components, layouts)
├── Auth/              Authentication, registration, Google OAuth, password reset,
│                      email verification, user management
├── Archives/          All 16 archive types — CRUD, business rules, type-specific models
├── Tags/              Global tag management and tag relationships
├── Search/            Full-text search, filtered search, abstraction layer
├── AI/                AI enhancement layer — classification, suggestions, chat
├── Telegram/          Telegram bot integration, message-to-archive mapping
├── Dashboard/         Dashboard overview, statistics, activity feed
├── Settings/          User profile, preferences, theme, API token management
└── Admin/             Admin user management, system-wide settings
```

### Core Module Responsibilities

The `Core` module owns all shared infrastructure:

- **Traits** — `UsesUlid`, shared model traits
- **Contracts** — `SearchEngineInterface`, `AiProviderInterface`, and other shared interfaces
- **Enums** — `ArchiveType`, `UserRole`, status enums
- **Exceptions** — Custom exception classes and error handling
- **Helpers** — Utility/helper functions
- **Providers** — Shared service providers
- **Livewire/Components** — Shared/reusable Livewire UI components
- **View/Layouts** — Application layouts (app.blade.php)
- **View/Components** — Shared Blade components
- **Support** — Base classes, support utilities

### Module Internal Structure

Each feature module may contain any of the following:

```text
Module/
├── Actions/           Single-responsibility action classes
├── DTOs/              Data transfer objects
├── Events/            Event classes
├── Listeners/         Event listeners
├── Http/
│   ├── Controllers/   Web and/or API controllers
│   ├── Middleware/     Module-specific middleware
│   └── Requests/      Form request validation
├── Livewire/          Livewire full-page and nested components
├── Models/            Eloquent models
├── Notifications/     Notification classes
├── Policies/          Authorization policies
├── Providers/         Module-specific service providers
├── Repositories/      Data access abstraction
├── Services/          Application business logic
├── Views/             Blade templates (Livewire and plain)
└── routes/            Module-specific route files (optional)
```

- **Module-specific contracts** (e.g., `SearchEngineInterface`) live inside the module.
  **Shared contracts** live under `Core/Contracts/`.
- Cross-module communication SHOULD use events, listeners, or contracts — never direct coupling.

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

Communication Rules

* All responses must be in English.
* All reports, plans, reviews, summaries, and architecture discussions must be written in English.
* Do not respond in Chinese, Japanese, Korean, or any other language unless explicitly requested.
* Code comments must be in English.
* Documentation must be in English.
