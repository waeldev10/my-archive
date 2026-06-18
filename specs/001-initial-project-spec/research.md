# Research: Initial Platform — My Archive

**Phase**: 0 (Research & Key Decisions)
**Date**: 2026-06-16
**Input**: [spec.md](spec.md), [plan.md](plan.md), constitution, docs/

## Architecture Decisions

### Decision 1: Hybrid Archive Table Architecture

**Status**: Resolved (from clarification session)

**Decision**: Single `archives` base table + type-specific extension tables.

Shared base fields:
- `id` (ULID), `user_id` (ULID, FK), `type` (enum/varchar), `title`, `description`
- `is_favorite` (boolean), timestamps (created_at, updated_at, deleted_at)

Extension tables for types with dedicated fields:
- `archive_links` — url, domain, preview_image
- `archive_images` — file_path, mime_type, width, height, alt_text
- `archive_files` — file_path, mime_type, file_size, original_name
- `archive_todos` — due_date, completed_at, priority
- `archive_plans` — start_date, end_date, status, progress
- `archive_projects` — start_date, end_date, status, repository_url
- `archive_courses` — provider, platform, completion_status, progress
- `archive_books` — author, isbn, pages, status, started_at, finished_at
- `archive_snippets` — code_language, code_content, source_url
- `archive_websites` — url, domain, feed_url
- `archive_journals` — entry_date, mood, location
- Archive types without dedicated fields (Note, Article, Idea, Bookmark,
  Prompt) store their content directly in the base `archives` table's
  `description` or extended via a shared `archive_contents` table if needed
  for full-text search.

**Rationale**:
- Normalization: common fields don't repeat across 16 tables
- Type safety: extension tables have proper column types for specific fields
- Extensibility: new archive types add one extension table, no schema changes
  to existing tables
- Query performance: listing all archives (dashboard, search) queries one table
- Search: single tsvector index on base table for cross-type searching

**Alternatives considered**:
- Single table with JSON metadata: rejected due to weak typing and query
  performance concerns
- 16 completely separate tables: rejected due to duplication of common fields
  and complexity of cross-type operations
- EAV pattern: rejected due to query complexity and lack of type safety

---

### Decision 2: Search — PostgreSQL Full-Text Search

**Status**: Resolved (from clarification session)

**Decision**: PostgreSQL tsvector/tsquery with weighted ranking, behind a
SearchEngineInterface. External engines deferred to post-MVP.

Weighting scheme:
- Title: weight A (highest, multiplier ~1.0)
- Description/content: weight B (medium, multiplier ~0.4)
- Tags: weight C (lower, multiplier ~0.2)

Search behavior:
- Search across `archives.title`, `archives.description`, and tag names
- Results grouped by archive type with counts
- Filtering by type, tag, and favorite status
- Ranking by ts_rank with the weighting above

**Rationale**:
- Zero infrastructure beyond PostgreSQL (self-hosted friendly)
- Weighted ranking provides relevant results without external engine
- Abstraction layer allows engine swap without business logic changes
- PostgreSQL FTS supports stemming, stop word removal, and dictionary-based
  normalization

**Alternatives considered**:
- LIKE/SIMILAR TO queries: rejected due to poor performance at scale
- Meilisearch/Typesense: excellent but adds infrastructure, deferred to post-MVP
- Elasticsearch: overkill for single-user/small-team deployment

---

### Decision 3: File Storage — Local with S3 Abstraction

**Status**: Resolved (from clarification session)

**Decision**: Local filesystem via Laravel filesystem abstraction. Files private,
served through auth-gated controller. S3 migration via config change only.

Storage layout: `storage/app/archives/{user_id}/{type}/{archive_id}.{ext}`

Access control:
- Upload controller validates file type, size, ownership
- Download controller authenticates user, authorizes ownership
- Files never placed in `public/` directory
- URL generation uses temporary signed URLs (or equivalent)

**Rationale**:
- Self-hosted friendly: no S3/MinIO required for MVP
- Laravel's filesystem abstraction makes S3 migration a config change
- Private files + application-layer auth prevents unauthorized access
- Organized directory structure simplifies backup and manual management

**Alternatives considered**:
- Public files with symlink: rejected for security concerns
- Database BLOB storage: rejected due to performance and backup size

---

### Decision 4: Authentication — Laravel Sanctum

**Status**: Resolved (from clarification session)

**Decision**: Laravel Sanctum with session-based auth for web SPA and personal
access tokens for external clients (Telegram, browser extensions, mobile apps).
API versioning via URL prefix `/api/v1/`.

Auth flows:
- Web: email/password registration with email verification
- Web: Google OAuth (implicit account creation)
- API: Sanctum personal access tokens, generated from user settings
- Rate limiting: 5 failed login attempts before temporary lockout

**Rationale**:
- Sanctum is the standard Laravel auth for SPAs and simple token APIs
- No OAuth complexity for MVP (Passport deferred to post-MVP)
- Session + token auth coexist natively in Sanctum

**Alternatives considered**:
- Laravel Passport: full OAuth2, overkill for v1 (no third-party consumers yet)
- JWT (tymondesigns/jwt-auth): additional dependency, no advantage over Sanctum

---

### Decision 5: AI Provider Abstraction

**Status**: Resolved (from specification)

**Decision**: Contract-driven AI abstraction with `AiProviderInterface`. Each
provider (DeepSeek, OpenAI, Gemini) implements the interface. Configuration
via user settings.

Interface methods:
- `classify(array $content): string` — suggest archive type
- `suggestTags(array $content): array` — generate tag suggestions
- `summarize(string $content): string` — generate summary
- `chat(string $query, array $context): string` — answer questions
- `retrieve(string $query, array $archives): string` — knowledge retrieval

Dual-mode chat:
- Global mode: retrieves archives matching query context, sends to AI
- Archive mode: scopes to single archive content, sends to AI

**Rationale**:
- Provider independence per constitution principle X
- Users bring their own API keys (no bundled AI service)
- Interface contract isolates business logic from provider specifics

**Alternatives considered**:
- Tight coupling to OpenAI: rejected (provider lock-in, violates constitution)
- No abstraction layer: rejected (every provider change would touch business logic)

---

### Decision 6: Telegram Bot Architecture

**Status**: Resolved (from specification)

**Decision**: Webhook-based Telegram bot using Laravel's routing. Bot
authenticates via Sanctum token configured by the user. Incoming messages
mapped to archive types based on content type.

Message mapping:
- Text messages → Note archive
- URLs in text → Link archive
- Photos with caption → Image archive
- Document/file → File archive
- Commands → /search, /recent, /help

**Rationale**:
- Webhooks are simpler than long-polling for self-hosted deployments
- User-configurable tokens keep auth in user control
- Message-to-type mapping provides intuitive archiving without explicit type
  selection

**Alternatives considered**:
- Long-polling (getUpdates): more complex, requires persistent process
- Single-purpose bot with fixed token: less flexible, less secure

---

## Technology Best Practices

### Laravel 12 Patterns

- Use Form Requests for validation in controllers
- Use Laravel Actions for single-responsibility operations
- Use Laravel Events for cross-module communication
- Use Eloquent with Repository pattern for data access abstraction
- Use DTOs for type-safe data transfer between layers
- Use Laravel's built-in rate limiting for API and auth endpoints
- Use model observers for side effects (e.g., updating search indexes)

### Livewire v4 Patterns

- Full-page components for CRUD views (archive type pages)
- Nested components for reusable UI (tag selector, search bar)
- Wire:model for form binding with real-time validation
- Event listeners for cross-component communication
- Lazy loading for non-critical dashboard sections

### Testing Strategy

- Feature tests for all CRUD endpoints (web + API)
- Unit tests for Services, Actions, DTOs
- Mock AI providers in tests — never call real AI APIs
- Mock HTTP client for Telegram webhook tests
- SQLite in-memory database for test speed
- Factory classes for all models
- Pest PHP with descriptive test names indicating user story coverage
