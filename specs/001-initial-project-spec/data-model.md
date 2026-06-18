# Data Model: Initial Platform — My Archive

**Phase**: 1 (Design)
**Date**: 2026-06-16
**Input**: [spec.md](spec.md), [research.md](research.md)

---

## Entity: User

**Table**: `users` (existing Laravel migration)

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK |
| name | string(255) | required |
| email | string(255) | required, unique |
| email_verified_at | timestamp | nullable |
| password | string(255) | nullable (null for OAuth-only users) |
| google_id | string(255) | nullable, unique |
| avatar | string(255) | nullable |
| role | enum: admin, user | default: user |
| remember_token | string(100) | nullable |
| timestamps | — | created_at, updated_at |

**Relationships**: hasMany Archives, hasMany ApiTokens, hasOne UserPreference

**Indexes**: email (unique), google_id (unique), role

---

## Entity: UserPreference

**Table**: `user_preferences`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK |
| user_id | ULID | FK → users, unique |
| theme | enum: light, dark, system | default: system |
| locale | string(10) | default: en |
| ai_provider | string(50) | nullable (null = AI disabled) |
| ai_api_key | text | nullable, encrypted |
| ai_model | string(100) | nullable |
| notifications_enabled | boolean | default: true |
| timestamps | — | created_at, updated_at |

**Relationships**: belongsTo User

---

## Entity: ApiToken

**Table**: `personal_access_tokens` (Laravel Sanctum)

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK |
| tokenable_type | string | morph |
| tokenable_id | ULID | morph |
| name | string(255) | required |
| token | string(64) | unique, hashed |
| abilities | text | nullable, JSON array |
| last_used_at | timestamp | nullable |
| expires_at | timestamp | nullable |
| timestamps | — | created_at, updated_at |

**Relationships**: morphTo tokenable (User)

**Notes**: Standard Laravel Sanctum table. Tokens shown once at creation,
hashed in database.

---

## Entity: Archive (Base)

**Table**: `archives`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK |
| user_id | ULID | FK → users, NOT NULL |
| type | enum: note, link, article, image, file, todo, plan, project, idea, bookmark, course, book, snippet, website, journal, prompt | NOT NULL |
| title | string(255) | required |
| description | text | nullable |
| is_favorite | boolean | default: false |
| soft_deletes | — | deleted_at |
| timestamps | — | created_at, updated_at |

**Relationships**: belongsTo User, morphToMany Tags (via taggables),
hasOne ArchiveLink / ArchiveImage / etc. (polymorphic or type-checked)

**Indexes**:
- (user_id, type) — for type-specific listings
- (user_id, created_at) — for dashboard timeline
- (user_id, is_favorite) — for favorites
- tsvector_search — GIN index on full-text search vector
- (user_id, deleted_at) — for soft-delete queries

**Full-Text Search**:
- Generated column: `search_vector tsvector` (populated via trigger or
  Laravel event)
- Components: coalesce(title, '') || ' ' || coalesce(description, '')
- Weighted: title (A), description (B)
- Updated on title/description change

---

## Entity: ArchiveLink

**Table**: `archive_links`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK (same as archives.id) |
| url | text | NOT NULL |
| domain | string(255) | nullable |
| preview_image | text | nullable |
| preview_description | text | nullable |
| timestamps | — | |

**Relationships**: belongsTo Archive (via shared ULID)

---

## Entity: ArchiveImage

**Table**: `archive_images`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK (same as archives.id) |
| file_path | string(255) | NOT NULL |
| mime_type | string(100) | nullable |
| width | integer | nullable |
| height | integer | nullable |
| file_size | integer | nullable (bytes) |
| alt_text | string(255) | nullable |
| timestamps | — | |

---

## Entity: ArchiveFile

**Table**: `archive_files`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK (same as archives.id) |
| file_path | string(255) | NOT NULL |
| mime_type | string(100) | nullable |
| file_size | integer | nullable (bytes) |
| original_name | string(255) | NOT NULL |
| timestamps | — | |

---

## Entity: ArchiveTodo

**Table**: `archive_todos`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK (same as archives.id) |
| due_date | date | nullable |
| completed_at | timestamp | nullable |
| priority | enum: low, medium, high | default: medium |
| timestamps | — | |

---

## Entity: ArchivePlan

**Table**: `archive_plans`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK (same as archives.id) |
| start_date | date | nullable |
| end_date | date | nullable |
| status | enum: draft, active, completed, cancelled | default: draft |
| progress | integer | default: 0, range: 0–100 |
| timestamps | — | |

---

## Entity: ArchiveProject

**Table**: `archive_projects`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK (same as archives.id) |
| start_date | date | nullable |
| end_date | date | nullable |
| status | enum: idea, planning, active, paused, completed, cancelled | default: idea |
| repository_url | text | nullable |
| timestamps | — | |

---

## Entity: ArchiveCourse

**Table**: `archive_courses`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK (same as archives.id) |
| provider | string(255) | nullable |
| platform | string(255) | nullable |
| completion_status | enum: not_started, in_progress, completed | default: not_started |
| progress | integer | default: 0, range: 0–100 |
| timestamps | — | |

---

## Entity: ArchiveBook

**Table**: `archive_books`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK (same as archives.id) |
| author | string(255) | nullable |
| isbn | string(20) | nullable |
| pages | integer | nullable |
| status | enum: to_read, reading, finished, abandoned | default: to_read |
| started_at | date | nullable |
| finished_at | date | nullable |
| timestamps | — | |

---

## Entity: ArchiveSnippet

**Table**: `archive_snippets`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK (same as archives.id) |
| code_language | string(50) | nullable |
| code_content | text | NOT NULL |
| source_url | text | nullable |
| timestamps | — | |

---

## Entity: ArchiveWebsite

**Table**: `archive_websites`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK (same as archives.id) |
| url | text | NOT NULL |
| domain | string(255) | nullable |
| feed_url | text | nullable |
| timestamps | — | |

---

## Entity: ArchiveJournal

**Table**: `archive_journals`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK (same as archives.id) |
| entry_date | date | NOT NULL |
| mood | string(50) | nullable |
| location | string(255) | nullable |
| timestamps | — | |

---

## Entity: Note, Article, Idea, Bookmark, Prompt

These archive types do not require extension tables. Their primary content
is stored in the `archives.description` field. If richer content storage is
needed, a shared `archive_contents` table can be added later.

---

## Entity: Tag

**Table**: `tags`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK |
| user_id | ULID | FK → users, NOT NULL |
| name | string(100) | NOT NULL |
| color | string(7) | nullable (hex, e.g., #3B82F6) |
| timestamps | — | created_at, updated_at |

**Relationships**: belongsTo User, morphToMany Archives (via taggables)

**Indexes**: (user_id, name) unique

**Soft Deletes**: no — tag deletion removes all associations

---

## Entity: Taggable (Pivot)

**Table**: `taggables`

| Column | Type | Constraints |
|--------|------|-------------|
| tag_id | ULID | FK → tags, PK |
| taggable_id | ULID | PK |
| taggable_type | string | PK |

**Indexes**: (taggable_id, taggable_type), (tag_id)

**Notes**: Laravel polymorphic many-to-many pivot. Allows any archive type
(and future entities) to be tagged.

---

## Entity: ActivityLog

**Table**: `activity_logs`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK |
| user_id | ULID | FK → users, NOT NULL |
| archive_id | ULID | FK → archives, nullable |
| action | enum: created, updated, deleted, restored, favorited, tagged | NOT NULL |
| description | string(255) | nullable |
| timestamps | — | created_at (no updated_at needed) |

**Indexes**: (user_id, created_at), (user_id, archive_id)

**Notes**: Immutable log — no updates, only inserts. Records user activity
for dashboard timeline.

---

## Entity: TelegramConnection

**Table**: `telegram_connections`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK |
| user_id | ULID | FK → users, unique |
| telegram_chat_id | string(100) | NOT NULL, unique |
| telegram_username | string(100) | nullable |
| is_active | boolean | default: true |
| connected_at | timestamp | NOT NULL |
| timestamps | — | created_at, updated_at |

**Relationships**: belongsTo User

---

## Entity: AiConversation

**Table**: `ai_conversations`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK |
| user_id | ULID | FK → users, NOT NULL |
| archive_id | ULID | FK → archives, nullable (null = global mode) |
| title | string(255) | nullable (auto-generated) |
| timestamps | — | created_at, updated_at |

**Relationships**: belongsTo User, belongsTo Archive (nullable), hasMany AiMessages

---

## Entity: AiMessage

**Table**: `ai_messages`

| Column | Type | Constraints |
|--------|------|-------------|
| id | ULID | PK |
| conversation_id | ULID | FK → ai_conversations, NOT NULL |
| role | enum: user, assistant | NOT NULL |
| content | text | NOT NULL |
| timestamps | — | created_at (immutable) |

**Relationships**: belongsTo AiConversation

---

## Entity: ArchivedArchive (Soft Delete)

Soft-deleted archives are retained in the `archives` table with a
`deleted_at` timestamp. Extension table rows are cascade-deleted when the
base archive is force-deleted.

**Restore**: Archives can be restored from trash within 30 days of deletion.
After 30 days, a scheduled cleanup job permanently removes them.

---

## Entity Relationships Diagram (Text)

```
User (1) ──< (N) Archive (base)
User (1) ──< (N) Tag
User (1) ──< (N) ActivityLog
User (1) ──1 (1) UserPreference
User (1) ──< (N) ApiToken (Sanctum)
User (1) ──< (N) TelegramConnection
User (1) ──< (N) AiConversation

Archive (1) ──1 (0..1) ArchiveLink
Archive (1) ──1 (0..1) ArchiveImage
Archive (1) ──1 (0..1) ArchiveFile
Archive (1) ──1 (0..1) ArchiveTodo
Archive (1) ──1 (0..1) ArchivePlan
Archive (1) ──1 (0..1) ArchiveProject
Archive (1) ──1 (0..1) ArchiveCourse
Archive (1) ──1 (0..1) ArchiveBook
Archive (1) ──1 (0..1) ArchiveSnippet
Archive (1) ──1 (0..1) ArchiveWebsite
Archive (1) ──1 (0..1) ArchiveJournal

Archive (N) >─< (N) Tag  (polymorphic via taggables)
Archive (1) ──< (N) ActivityLog
Archive (1) ──< (N) AiConversation (nullable FK)

AiConversation (1) ──< (N) AiMessage
```
