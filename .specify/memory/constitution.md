<!--
== Sync Impact Report ==
Version change: ∅ → 1.0.0 (initial creation)
Modified principles: N/A (all new)
Added sections:
  - Core Principles (I–XII): All 12 principles
  - Technical Decisions: Binding technology stack table
  - Application Architecture: Monolith structure, layers, modules table
  - Domain Requirements: Archive types, tags, roles, AI enhancement rules
  - Governance: Amendment procedure, versioning policy, compliance review
Removed sections: N/A
Templates requiring updates:
  - .specify/templates/plan-template.md ✅ Constitution Check gate already present – no changes needed
  - .specify/templates/spec-template.md ✅ No constitution-specific references – no changes needed
  - .specify/templates/tasks-template.md ✅ No constitution-specific references – no changes needed
  - .specify/templates/commands/* ✅ No command templates exist yet
Follow-up TODOs: None
-->

# My Archive Constitution

## Core Principles

### I. Simplicity First

Prefer simple solutions over complex ones. Avoid unnecessary abstractions,
premature optimization, and over-engineering. Every piece of complexity MUST
be justified by a concrete, current requirement — never by hypothetical future
needs. Simple code is easier to test, debug, and maintain.

### II. CRUD First

Every feature MUST be fully usable through traditional CRUD interfaces. AI,
automation, and enhancement layers are additive — the application MUST remain
fully functional without them. All core data operations MUST be creatable,
readable, updatable, and deletable through direct user interaction.

### III. AI Enhancement, Not AI Dependency

Artificial Intelligence is strictly an enhancement layer. AI MUST improve
productivity, automation, classification, and search without becoming a hard
dependency. The application MUST function completely when AI services are
unavailable. No core workflow MUST require an AI provider response to complete
a primary user action.

### IV. API First Architecture

Every business capability MUST be accessible through a REST API. Web interfaces
SHOULD consume the same application services used by API endpoints. Future
clients — mobile applications, browser extensions, desktop applications, and
Telegram integrations — MUST be supported without major architectural changes.
API versioning MUST be in place from the first release.

### V. Modular Architecture

The system MUST be organized into clear modules with well-defined
responsibilities. Module boundaries MUST be respected: cross-module
communication SHOULD happen through services, events, or contracts — not by
direct coupling. Each module MUST be independently understandable and
maintainable.

### VI. Separation of Concerns

Business logic MUST never live inside controllers. Controllers MUST delegate
work to Services, Actions, and domain components. The layer stack MUST be:
Controller → Service/Action → Repository → Model. Views MUST only present
data, never compute or transform it.

### VII. Maintainability Over Cleverness

Readable, straightforward code is preferred over clever or highly abstract
implementations. Future maintainers (including the author, six months later)
MUST be able to understand the system without deep context. Follow consistent
naming conventions, coding standards, project structure, API responses, and
database design across all modules.

### VIII. Security by Default

Security is a core, non-negotiable requirement. The system MUST include:

- Authentication (email + Google OAuth)
- Authorization (Admin and User roles)
- Rate limiting on all public endpoints
- Input validation on all data entry points
- Secure file handling (validation, sanitization, access control)
- Secure defaults for all configuration
- Email verification and password reset flows
- SQL injection prevention through parameterized queries

### IX. Self-Hosted Friendly

The platform MUST be easy to install and run on personal servers and VPS
environments. Avoid unnecessary infrastructure requirements. Deployment
SHOULD NOT require Kubernetes, container orchestration, or any service beyond
a standard LEMP/LEPP stack with Redis. Minimal, clear setup documentation is
required.

### X. Provider Independence

The system MUST NOT be coupled to any single external provider — whether AI,
authentication, email, or storage. AI integrations MUST be implemented through
an abstraction layer and contracts. Supported AI providers include DeepSeek,
OpenAI, and Gemini. Adding a new provider MUST NOT require changes to
business logic.

### XI. Testability

Core business logic MUST be testable. Critical functionality MUST be covered
by automated tests using Pest. Tests MUST be reliable, deterministic, and
fast — never dependent on external services. Mock AI providers, HTTP clients,
and external APIs in tests. Tests SHOULD be written alongside or before the
code they verify. Untested code SHOULD be treated as unfinished.

### XII. Performance Awareness

Performance MUST be considered from the beginning, not retrofitted. Prefer
efficient queries, proper indexing strategies, Redis caching, and asynchronous
processing (queues) where appropriate. Database queries MUST be optimized:
prevent N+1 problems, use eager loading, leverage indexes, and profile before
optimizing.

## Technical Decisions

The following technical decisions are binding and MUST be followed unless
explicitly amended by the Governance process.

| Domain | Decision | Rationale |
|--------|----------|-----------|
| Backend Framework | Laravel 13 (PHP 8.3+) | Rapid development, rich ecosystem |
| Frontend Framework | Livewire v4 + Alpine.js + Tailwind CSS v4 | Full-stack reactivity within Laravel, minimal JS tooling |
| Database | PostgreSQL | Reliability, full-text search, JSON support |
| Cache & Queue | Redis | Performance, shared cache/queue layer |
| Testing | Pest PHP | Expressive, Laravel-native testing framework |
| Identifiers | ULID | Sortable, unique, URL-safe across all tables |
| API Style | REST API | Universal client compatibility |
| Auth | Laravel Sanctum + Google OAuth | Simple API token auth + SSO |
| Telegram | Integration in V1 | Primary external integration channel |
| AI Providers | Abstraction Layer (DeepSeek, OpenAI, Gemini) | Swappable, contract-driven |

## Application Architecture

### Monolith with Clear Boundaries

This is a monolithic Laravel application containing both Web Interface and REST
API within the same codebase. Monolith does NOT mean monolithic design —
modules and layers MUST enforce clear boundaries and separation of concerns.

### Architecture Layers

Each feature module MUST follow this internal layer pattern:

- **Controller**: Thin — delegates immediately to Services or Actions
- **Service Layer**: Application business logic and orchestration
- **Repository Pattern**: Data access abstraction per entity
- **DTOs**: Data transfer objects for API and web boundary communication
- **Actions**: Single-responsibility, reusable operations
- **Events & Listeners**: Decoupled side effects and cross-module communication

The **Core** module provides shared infrastructure consumed by all feature modules:
shared contracts, traits, enums, base classes, layouts, and reusable UI components.
Feature modules MUST pull shared dependencies from Core rather than duplicating them.

### Modules

| Module | Responsibility |
|--------|---------------|
| **Core** | Shared infrastructure — traits, contracts, enums, exceptions, helpers, providers, support classes, shared Livewire components, layouts |
| **Auth** | Registration, login, Google OAuth, password reset, email verification, user profile |
| **Archives** | All 16 archive types — CRUD, type-specific models, business rules, all archive views |
| **Tags** | Global tag management and polymorphic tag relationships |
| **Search** | Full-text search, filtered search, search abstraction layer |
| **AI** | Classification, tag suggestions, summaries, AI chat, knowledge retrieval, AI provider abstraction |
| **Telegram** | Bot integration, message-to-archive mapping, AI interaction via Telegram |
| **Dashboard** | Statistics, recent archives, quick actions, activity overview |
| **Settings** | User profile, theme preferences, API token management, AI provider configuration |
| **Admin** | Admin user management, system-wide settings |

**Cross-cutting**: The `Core` module provides shared infrastructure consumed by all modules.
Shared contracts live under `Core/Contracts/`. Module-specific contracts stay inside their
own module. The `API` is not a module — it is a cross-cutting concern delivered by each
module's `Http/Controllers/Api/` subdirectory.

## Domain Requirements

### Archive Types

- Each archive type has its own database table
- Each archive type has its own Eloquent Model
- Each archive type has its own CRUD operations
- Each archive type has its own pages and views
- All archive tables MUST use ULID identifiers
- 16 archive types: Note, Link, Article, Image, File, Todo, Plan, Project,
  Idea, Bookmark, Course, Book, Snippet, Website, Journal, Prompt

### Tags

- Tags are global across the entire system — not scoped per archive type
- A single tag may be attached to any archive type
- Tag management is a shared concern, not per-module

### Roles

- Only two roles exist: **Admin** and **User**
- No complex permission or ACL system is required
- Authorization logic MUST be simple role-based checks

### AI Is Enhancement Only

- AI features are strictly optional and additive
- The application MUST remain fully functional with zero AI provider access
- AI features include: classification, tag suggestions, summaries, search
  assistance, knowledge retrieval, and archive creation assistance
- AI Provider Abstraction Layer enables provider switching without business
  logic changes

## Governance

This Constitution supersedes all other ad-hoc project practices and conventions.
It is the source of truth for architectural, technical, and domain decisions.

### Amendment Procedure

1. **Proposal**: Document the proposed change with full rationale
2. **Review**: All changes MUST be reviewed for consistency with existing
   principles and sections
3. **Approval**: Changes require maintainer approval
4. **Propagation**: Update all dependent templates and documentation

### Versioning Policy

- **MAJOR** (X.0.0): Backward-incompatible governance changes, principle
  removals, or redefinitions
- **MINOR** (0.X.0): New principles or sections, materially expanded guidance
- **PATCH** (0.0.X): Clarifications, wording fixes, typo corrections,
  non-semantic refinements

### Compliance Review

- Every implementation plan MUST pass the **Constitution Check** gate
- Complexity violations (exceeding defined architecture patterns) MUST be
  documented in the Complexity Tracking section of the implementation plan
- All code reviews and PRs MUST verify constitutional compliance
- Vague language ("should", "maybe", "nice to have") in principles is replaced
  with RFC 2119 keywords (MUST, SHOULD, MAY) for precise intent

**Rationale**: This governance ensures the constitution remains a living
document that evolves deliberately while preventing scope creep, architectural
drift, and undocumented deviations.

**Version**: 1.0.0 | **Ratified**: 2026-06-16 | **Last Amended**: 2026-06-16
