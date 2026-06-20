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

# Clear all caches
php artisan optimize:clear

# Warm caches
php artisan optimize
```

## Architecture

### Domain-Based Modular Architecture

The project uses a domain-based modular architecture under `Modules/` instead of the standard
Laravel `app/` structure. Each domain module is self-contained with its own classes, views,
routes, and tests.

```text
Modules/
├── Core/              Shared infrastructure (traits, contracts, enums, exceptions, helpers,
│                      providers, support classes, shared Livewire components, layouts,
│                      shared Blade components)
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
- **View/Components** — Shared Blade components (Button, Input, Card, Badge, Alert, etc.)
- **View/Layouts** — Application layouts (app.blade.php)
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
│   │   └── Web/       Web controllers (application entry points)
│   │   └── Api/       API controllers
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
│   ├── pages/         Page-level Blade views (entry point for web controllers)
│   ├── components/    Module-specific Blade components
│   └── archives/      (example) Livewire view fragments
└── routes/            Module-specific route files (optional)
    ├── web.php        Web routes
    └── api.php        API routes
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
- **Font**: Tajawal (200–900 weights) via Bunny CDN — Arabic-first, wide Latin support
- **Direction**: RTL (dir="rtl", lang="ar")
- **Entry points**: `resources/css/app.css`, `resources/js/app.js`

### Testing

- **Framework**: Pest PHP v4 with `pestphp/pest-plugin-laravel`
- **Test suites**: Unit (`tests/Unit/`) and Feature (`tests/Feature/`)
- **Base class**: `Tests\TestCase` (Laravel's base TestCase)
- Feature tests extend `TestCase` and run without `RefreshDatabase` by default (commented out in `tests/Pest.php`)

## Frontend Architecture Standards

These standards are MANDATORY for all future module development.

### Application Entry Pattern (BINDING)

```
Route → Controller → Blade Page → Livewire Components
```

- **Controllers** are the application entry points for web routes
- **Direct Livewire routing** (`Route::get(..., Component::class)`) is NOT permitted
- Controllers return Blade page views that contain `@livewire()` calls
- See `Modules/Archives/Http/Controllers/Web/ArchiveController.php` for the reference pattern

```php
// ✅ CORRECT — Controller → Blade → Component
Route::get('archives/{type}', [ArchiveController::class, 'index']);

// ❌ INCORRECT — Direct Livewire routing
Route::get('archives/{type}', ArchiveList::class);
```

```blade
{{-- Page view (Modules/Archives/Views/pages/index.blade.php) --}}
@extends('core::layouts.app')
@section('content')
    @livewire('archives.list', ['type' => $type], key($type))
@endsection
```

### Design Token Usage (BINDING)

All views MUST use CSS custom properties from `resources/css/app.css`. **No hardcoded Tailwind color classes.**

```blade
{{-- ✅ CORRECT -- Token-based --}}
class="text-[var(--color-foreground)] bg-[var(--color-surface)]"
class="bg-[var(--color-primary-600)] text-white"

{{-- ❌ INCORRECT -- Hardcoded colors --}}
class="text-gray-900 dark:text-white"
class="bg-indigo-600 text-white"
```

**Available semantic tokens:**

| Token | Purpose | Light Value | Dark Value |
|-------|---------|-------------|------------|
| `--color-primary-*` | Main actions, links | Indigo scale | Indigo scale |
| `--color-secondary-*` | Neutral surfaces | Slate scale | Slate scale |
| `--color-accent-*` | Highlights | Purple scale | Purple scale |
| `--color-success-*` | Success states | Green scale | Green scale |
| `--color-warning-*` | Warning states | Amber scale | Amber scale |
| `--color-danger-*` | Error/danger states | Red scale | Red scale |
| `--color-info-*` | Info states | Cyan scale | Cyan scale |
| `--color-surface` | Card/component background | white | #0a0a0a |
| `--color-surface-secondary` | Hover/secondary bg | #f9fafb | #111827 |
| `--color-background` | Page background | white | #0a0a0a |
| `--color-foreground` | Primary text | #111827 | #f9fafb |
| `--color-foreground-secondary` | Secondary text | #6b7280 | #9ca3af |
| `--color-foreground-muted` | Muted text | #9ca3af | #6b7280 |
| `--color-border` | Borders | #e5e7eb | #1f2937 |

Or use Tailwind's `primary-*`, `secondary-*`, `accent-*`, `success-*`, `warning-*`, `danger-*`, `info-*` classes:

```blade
{{-- ✅ ALSO CORRECT -- Tailwind alias tokens --}}
class="text-secondary-500 bg-surface border border-border"
class="bg-primary-600 text-white hover:bg-primary-700"
```

### Shared Blade Components (BINDING)

Use shared components from `Modules/Core/Views/components/` (available as `x-core::*`):

```blade
<x-core::button variant="primary" :href="route(...)">Label</x-core::button>
<x-core::button variant="secondary" type="submit" loading wire="target">Save</x-core::button>
<x-core::input label="Email" name="email" type="email" model="email" blur required />
<x-core::textarea label="Description" name="desc" model="desc" rows="4" />
<x-core::select label="Status" name="status" model="status" :options="[...]" />
<x-core::card>Content</x-core::card>
<x-core::badge variant="primary">Tag</x-core::badge>
<x-core::badge variant="success" removable>Item</x-core::badge>
<x-core::alert type="success" :message="session('success')" dismissible />
<x-core::alert type="danger" :message="$error" />
<x-core::empty-state title="No items" description="Get started..." />
<x-core::page-header title="Page Title" :back-route="route(...)">
    <x-slot:actions>...</x-slot:actions>
</x-core::page-header>
<x-core::modal name="confirm-delete" title="Confirm" max-width="md">
    Content
    <x-slot:footer>Action buttons</x-slot:footer>
</x-core::modal>
```

**Do not reimplement** buttons, inputs, cards, badges, alerts, etc. in module views.

### RTL Requirements (BINDING)

- Application is Arabic-first: `lang="ar"`, `dir="rtl"`
- Use RTL-safe CSS utilities:
  - `start-*` / `end-*` instead of `left-*` / `right-*`
  - `ms-*` / `me-*` instead of `ml-*` / `mr-*`
  - `ps-*` / `pe-*` instead of `pl-*` / `pr-*`
  - `text-start` / `text-end` instead of `text-left` / `text-right`
  - `border-s-*` / `border-e-*` instead of `border-l-*` / `border-r-*`
- When unavoidable, wrap in conditional based on locale
- SVG directional arrows: add `class="rtl-flip"` for horizontal mirroring

### Theme & Dark Mode (BINDING)

- All views MUST include `dark:` variants OR use CSS custom properties (which handle dark mode automatically)
- Theme toggle MUST persist to `localStorage` and `user_preferences.theme`
- `system` mode is the default — respect `prefers-color-scheme`
- Theme transitions use the `theme-transitioning` CSS class on `<html>`

### Font (BINDING)

- Default: **Tajawal** via `--font-sans` in `@theme`
- No hardcoded font families in module views
- Font configuration is in ONE location: `resources/css/app.css`

### Typography

- Headings: `font-bold` (Tajawal 700)
- Body: `text-sm` or `text-base`, `font-normal` (Tajawal 400)
- Labels: `text-sm font-medium` (Tajawal 500)
- Monospace (code): `font-mono`

## Livewire 4 Performance Rules

These rules are MANDATORY for all Livewire components.

### 1. Controller → Blade → Component Pattern
- Web controllers (not Livewire components) are the application entry points
- Components render only their fragment — no `->layout()` in render()
- Register components in service providers via `Livewire::component()`

### 2. Hydration Rules
- **`wire:model.live`**: Only for real-time interactions (search, toggle preview)
- **`wire:model.blur`**: Default for form inputs — updates on blur
- **`wire:model` (no modifier)**: Use only when immediate sync is needed
- Avoid excessive model updates; each triggers a hydration cycle

### 3. Lazy Loading
- Apply `#[Lazy]` to components that load expensive data on render
- Use `#[Computed]` for expensive derived properties instead of recomputing on every render

### 4. Query Rules
- Never run Eloquent queries directly inside `render()` methods
- Use computed properties (`get*Property()` methods) for cached queries
- Move business logic to Services (`Modules/*/Services/`)
- Move data access to Repositories (`Modules/*/Repositories/`)
- Use eager loading (`with()`) to prevent N+1 queries

### 5. wire:key Usage
- Every looped element MUST have a unique `wire:key`
- Use the model's primary key: `wire:key="{{ $item->id }}"`
- Nested Livewire components MUST have unique keys

### 6. Component Communication
- Prefer `$emit()` / `$on()` for parent-child communication
- Use `$dispatch()` for Livewire v4 event dispatching
- Pass data via props in `@livewire()` or `<livewire:>` tags
- Avoid excessive `$wire` calls from Alpine

### 7. Pagination
- Use `Livewire\WithPagination` trait
- Reset page on search/filter changes: `$this->resetPage()`
- Keep `per_page` reasonable (20 is the default)

### 8. Loading States
- Use `wire:loading` / `wire:loading.attr="disabled"` instead of manual `$isLoading` properties where possible
- Use `wire:target` to scope loading indicators to specific actions

### 9. Nested Components & Islands
- Break complex pages into smaller Livewire components
- Use `#[Isolate]` to prevent unnecessary parent re-renders
- Pass only required data as props — never full models

## Example: Complete Module Pattern

```
Modules/Example/
├── Http/
│   └── Controllers/
│       └── Web/
│           └── ExampleController.php     ← Entry point
├── Livewire/
│   └── ExampleList.php                   ← Livewire component
├── Providers/
│   └── ExampleServiceProvider.php        ← Registers routes, components, views
├── Views/
│   ├── pages/
│   │   └── index.blade.php              ← Page view @extends layout
│   └── livewire/
│       └── index.blade.php              ← Component fragment
└── routes/
    └── web.php                          ← Routes using controller
```

For additional context about the project architecture, data model, API
contracts, and implementation plan, read the feature plan:
specs/001-initial-project-spec/plan.md

Related design artifacts:
- Data model: specs/001-initial-project-spec/data-model.md
- API contracts: specs/001-initial-project-spec/contracts/
- Quickstart guide: specs/001-initial-project-spec/quickstart.md
- Research & decisions: specs/001-initial-project-spec/research.md

Communication Rules

* All responses must be in English.
* All reports, plans, reviews, summaries, and architecture discussions must be written in English.
* Do not respond in Chinese, Japanese, Korean, or any other language unless explicitly requested.
* Code comments must be in English.
* Documentation must be in English.
