# Implementation Plan: Initial Platform — My Archive

**Branch**: `001-initial-project-spec` | **Date**: 2026-06-16 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `specs/001-initial-project-spec/spec.md`

**Note**: This template is filled in by the `/speckit-plan` command. See `.specify/templates/plan-template.md` for the execution workflow.

## Summary

Build the complete My Archive platform — a personal knowledge and archive
management system with a Laravel 12 backend, Livewire v4 frontend, REST API,
global tags, full-text search, AI enhancement layer, and Telegram integration.
The system supports 16 archive types (Note, Link, Article, Image, File, Todo,
Plan, Project, Idea, Bookmark, Course, Book, Snippet, Website, Journal, Prompt)
with per-type CRUD and a shared base table architecture.

## Technical Context

**Language/Version**: PHP 8.3+, Laravel 12

**Primary Dependencies**:
- Livewire v4 + Alpine.js + Tailwind CSS v4 (frontend)
- PostgreSQL 16+ (database)
- Redis (cache + queue)
- Laravel Sanctum (API auth)
- Pest PHP (testing)

**Storage**: PostgreSQL (primary data), Redis (cache/queue), local filesystem
behind Laravel filesystem abstraction (user uploads, S3-migratable)

**Testing**: Pest PHP (Feature + Unit tests), SQLite in-memory for test DB,
mocked AI/HTTP clients for external services

**Target Platform**: Linux VPS (LEMP/LEPP stack), Windows/macOS for development

**Project Type**: Monolithic web application with REST API (same codebase)

**Performance Goals**:
- Page load < 500ms for standard CRUD operations
- Search results < 2s for up to 10,000 archives per user
- API response < 300ms for standard endpoints (P95)

**Constraints**:
- All entities use ULID identifiers
- Self-hosted friendly — no infrastructure beyond PostgreSQL + Redis
- AI features strictly optional — full functionality without AI
- 16 archive types with hybrid table architecture (base + type-specific tables)

**Scale/Scope**: Single-user to small-team deployments (< 100 users per instance)

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

### Core Principles Alignment

| Principle | Compliance | Notes |
|-----------|------------|-------|
| I. Simplicity First | ✅ Pass | Hybrid archive table design avoids over-engineering while remaining flexible |
| II. CRUD First | ✅ Pass | Every archive type has independent CRUD — no AI dependency |
| III. AI Enhancement, Not AI Dependency | ✅ Pass | All AI features optional; system fully functional without AI |
| IV. API First Architecture | ✅ Pass | REST API at `/api/v1/` covers all business capabilities from day one |
| V. Modular Architecture | ✅ Pass | Clear module boundaries: Archives, Tags, Search, AI, Telegram, etc. |
| VI. Separation of Concerns | ✅ Pass | Controller → Service/Action → Repository → Model layering enforced |
| VII. Maintainability Over Cleverness | ✅ Pass | Hybrid schema, Sanctum auth, PostgreSQL FTS — standard, well-documented choices |
| VIII. Security by Default | ✅ Pass | Sanctum tokens, rate limiting, email verification, ownership enforcement |
| IX. Self-Hosted Friendly | ✅ Pass | No infrastructure beyond PostgreSQL + Redis; local filesystem storage |
| X. Provider Independence | ✅ Pass | AI abstraction layer; filesystem abstraction; search abstraction |
| XI. Testability | ✅ Pass | Pest PHP with mocked external services; SQLite in-memory test DB |
| XII. Performance Awareness | ✅ Pass | PostgreSQL FTS, proper indexing, Redis caching, queue for async tasks |

### Technical Decisions Alignment

| Decision | Compliance | Notes |
|----------|------------|-------|
| Laravel 12 | ✅ Pass | |
| Livewire v4 + Alpine.js | ✅ Pass | |
| Tailwind CSS v4 | ✅ Pass | |
| PostgreSQL | ✅ Pass | |
| Redis | ✅ Pass | |
| Pest | ✅ Pass | |
| ULID | ✅ Pass | All archive tables use ULID |
| REST API | ✅ Pass | `/api/v1/*` with Sanctum |
| Telegram V1 | ✅ Pass | Bot integration via API tokens |
| AI Abstraction Layer | ✅ Pass | Contract-driven AI provider architecture |

### Domain Requirements Alignment

| Requirement | Compliance | Notes |
|-------------|------------|-------|
| Each archive type has own table | ✅ Pass | Hybrid: base + per-type extension tables |
| Each archive type has own CRUD | ✅ Pass | Type-specific controllers, services, views |
| Global tags across system | ✅ Pass | Single tags table, polymorphic relationships |
| Roles: Admin and User | ✅ Pass | Simple role-based authorization |
| AI is enhancement only | ✅ Pass | All AI features optional |

**Gate Status**: ✅ PASS — no violations requiring Complexity Tracking.

## Project Structure

### Documentation (this feature)

```text
specs/001-initial-project-spec/
├── plan.md                   # This file (/speckit-plan command output)
├── research.md               # Phase 0 output (/speckit-plan command)
├── data-model.md             # Phase 1 output (/speckit-plan command)
├── quickstart.md             # Phase 1 output (/speckit-plan command)
├── contracts/                # Phase 1 output (/speckit-plan command)
│   ├── api-archives.md
│   ├── api-auth.md
│   ├── api-tags.md
│   ├── api-search.md
│   ├── api-dashboard.md
│   ├── api-settings.md
│   ├── api-admin.md
│   ├── api-ai.md
│   └── api-telegram.md
└── tasks.md                  # Phase 2 output (/speckit-tasks command)
```

### Source Code (repository root)

```text
app/
├── Actions/
├── Console/
│   └── Commands/
├── DTOs/
├── Enums/
├── Events/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   └── V1/
│   │   └── Web/
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
├── Listeners/
├── Livewire/
├── Models/
├── Providers/
├── Repositories/
├── Services/
│   ├── Archive/
│   ├── AI/
│   ├── Search/
│   └── Telegram/
└── Traits/

config/
database/
├── migrations/
└── seeders/

resources/
├── css/
├── js/
└── views/
    ├── components/
    ├── layouts/
    └── livewire/
        ├── archives/
        ├── auth/
        ├── dashboard/
        ├── tags/
        ├── search/
        ├── ai/
        ├── settings/
        ├── admin/
        └── telegram/

routes/
├── web.php
├── api.php
└── console.php

tests/
├── Feature/
├── Unit/

docs/
```

**Structure Decision**: Standard Laravel 12 monolithic structure with clear
module organization under `app/Services/` and `app/Livewire/`. Each domain
module (Archives, Tags, Search, AI, Telegram) gets its own service directory.
API controllers live under `Http/Controllers/Api/V1/` for versioned separation.
Web controllers use Livewire full-stack components.

## Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

No violations detected. Constitution Check passes cleanly.
