---

description: "Task list for implementing the My Archive platform — 9 user stories across 12 phases"
---

# Tasks: Initial Platform — My Archive

**Input**: Design documents from `specs/001-initial-project-spec/`

**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/

**Tests**: No explicit TDD requirement — test tasks are not included. Each phase checkpoint includes validation criteria.

**Organization**: Tasks grouped by user story for independent implementation. Phases must run in order; tasks within a phase marked `[P]` are parallelizable.

## Path Conventions

- All paths are relative to the repository root: `E:\OpenSource\My-Archive\`
- Laravel app code under `app/`, database under `database/`, tests under `tests/`
- Frontend views under `resources/views/`, CSS under `resources/css/`, JS under `resources/js/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Install additional Laravel dependencies, configure packages, ensure project can build.

- [x] T001 Install Laravel Livewire (`composer require livewire/livewire:^4.0`)
- [x] T002 [P] Install Laravel Sanctum (`composer require laravel/sanctum`) and publish config
- [x] T003 [P] Install Alpine.js via NPM (`npm install alpinejs`)
- [x] T004 Install Laravel Socialite for Google OAuth (`composer require laravel/socialite`)
- [x] T005 [P] Verify `composer run dev` boots all processes (server, queue, logs, Vite)

**Checkpoint**: Dev servers start without errors; Livewire, Sanctum, Socialite declared in composer.json.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Database schema, ULID infrastructure, base models, API routing, Redis config — MUST complete before any user story.

- [x] T006 Enable ULID support: add `use Illuminate\Database\Eloquent\Concerns\HasUlids;` trait and create `app/Traits/UsesUlid.php` base trait for all models
- [x] T007 Create migration `database/migrations/xxxx_add_ulid_google_id_role_to_users_table.php` — add google_id (string, nullable, unique), avatar (string, nullable), role (string, default 'user'), change id to ULID pattern
- [x] T008 Create migration `database/migrations/xxxx_create_user_preferences_table.php` — ULID id, user_id FK, theme (string default 'system'), locale (string default 'en'), ai_provider (nullable), ai_api_key (nullable, encrypted), ai_model (nullable), notifications_enabled (bool default true), timestamps
- [x] T009 [P] Create migration `database/migrations/xxxx_create_archives_table.php` — ULID id, user_id FK, type (string), title (string 255), description (text nullable), is_favorite (bool default false), soft-deletes, timestamps. Add composite indexes: (user_id, type), (user_id, created_at), (user_id, is_favorite)
- [x] T010 [P] Create migration `database/migrations/xxxx_create_archive_links_table.php` — ULID id (PK, same as archives.id), url (text), domain (nullable), preview_image (nullable), preview_description (nullable), timestamps
- [x] T011 [P] Create migration `database/migrations/xxxx_create_archive_images_table.php` — ULID id (PK), file_path (string 255), mime_type (nullable), width (int nullable), height (int nullable), file_size (int nullable), alt_text (nullable), timestamps
- [x] T012 [P] Create migration `database/migrations/xxxx_create_archive_files_table.php` — ULID id (PK), file_path (string 255), mime_type (nullable), file_size (int nullable), original_name (string 255), timestamps
- [x] T013 [P] Create migration `database/migrations/xxxx_create_archive_todos_table.php` — ULID id (PK), due_date (date nullable), completed_at (timestamp nullable), priority (string default 'medium'), timestamps
- [x] T014 [P] Create migration `database/migrations/xxxx_create_archive_plans_table.php` — ULID id (PK), start_date (date nullable), end_date (date nullable), status (string default 'draft'), progress (int default 0), timestamps
- [x] T015 [P] Create migration `database/migrations/xxxx_create_archive_projects_table.php` — ULID id (PK), start_date (date nullable), end_date (date nullable), status (string default 'idea'), repository_url (text nullable), timestamps
- [x] T016 [P] Create migration `database/migrations/xxxx_create_archive_courses_table.php` — ULID id (PK), provider (nullable), platform (nullable), completion_status (string default 'not_started'), progress (int default 0), timestamps
- [x] T017 [P] Create migration `database/migrations/xxxx_create_archive_books_table.php` — ULID id (PK), author (nullable), isbn (string 20 nullable), pages (int nullable), status (string default 'to_read'), started_at (date nullable), finished_at (date nullable), timestamps
- [x] T018 [P] Create migration `database/migrations/xxxx_create_archive_snippets_table.php` — ULID id (PK), code_language (nullable), code_content (text), source_url (text nullable), timestamps
- [x] T019 [P] Create migration `database/migrations/xxxx_create_archive_websites_table.php` — ULID id (PK), url (text), domain (nullable), feed_url (text nullable), timestamps
- [x] T020 [P] Create migration `database/migrations/xxxx_create_archive_journals_table.php` — ULID id (PK), entry_date (date), mood (string 50 nullable), location (string 255 nullable), timestamps
- [x] T021 [P] Create migration `database/migrations/xxxx_create_tags_table.php` — ULID id, user_id FK, name (string 100), color (string 7 nullable), timestamps; unique index on (user_id, name)
- [x] T022 [P] Create migration `database/migrations/xxxx_create_taggables_table.php` — tag_id ULID FK, taggable_id ULID, taggable_type string; indexes on (taggable_id, taggable_type) and (tag_id)
- [x] T023 [P] Create migration `database/migrations/xxxx_create_activity_logs_table.php` — ULID id, user_id FK, archive_id FK nullable, action (string), description (string 255 nullable), created_at timestamp; indexes on (user_id, created_at) and (user_id, archive_id)
- [x] T024 [P] Create migration `database/migrations/xxxx_create_telegram_connections_table.php` — ULID id, user_id FK unique, telegram_chat_id (string 100, unique), telegram_username (nullable), is_active (bool default true), connected_at timestamp, timestamps
- [x] T025 [P] Create migration `database/migrations/xxxx_create_ai_conversations_table.php` — ULID id, user_id FK, archive_id FK nullable, title (nullable), timestamps
- [x] T026 [P] Create migration `database/migrations/xxxx_create_ai_messages_table.php` — ULID id, conversation_id FK, role (string), content (text), created_at timestamp
- [x] T027 Add PostgreSQL full-text search migration: add generated `search_vector tsvector` column to `archives` table with GIN index, weighted by title (A) and description (B)
- [x] T028 Configure Redis in `config/database.php` (redis session/cache/queue) — update `.env.example` and `config/cache.php`, `config/session.php`, `config/queue.php`
- [x] T029 [P] Update `app/Models/User.php` — add HasUlids trait, add google_id/avatar/role fillable fields, add role enum logic (admin, user), add relationship methods: archives(), tags(), preferences(), telegramConnection(), aiConversations(), activityLogs()
- [x] T030 [P] Create `app/Models/UserPreference.php` — fillable fields, belongsTo User relationship
- [x] T031 [P] Create `app/Models/Archive.php` — HasUlids, soft-deletes, belongsTo User, morphToMany Tags, polymorphic hasOne to type-specific models (scope by type column), cast is_favorite to bool
- [x] T032 [P] Create type-specific models: `app/Models/ArchiveLink.php`, `ArchiveImage.php`, `ArchiveFile.php`, `ArchiveTodo.php`, `ArchivePlan.php`, `ArchiveProject.php`, `ArchiveCourse.php`, `ArchiveBook.php`, `ArchiveSnippet.php`, `ArchiveWebsite.php`, `ArchiveJournal.php` — each with ULID PK, belongsTo Archive
- [x] T033 [P] Create `app/Models/Tag.php` — fillable, belongsTo User, morphToMany Archives
- [x] T034 [P] Create `app/Models/ActivityLog.php` — immutable (no updated_at), belongsTo User, belongsTo Archive (nullable)
- [x] T035 [P] Create `app/Models/TelegramConnection.php` — belongsTo User
- [x] T036 [P] Create `app/Models/AiConversation.php` and `AiMessage.php` — belongsTo User, belongsTo Archive (nullable), hasMany messages
- [x] T037 [P] Create `app/Enums/ArchiveType.php` — backed enum with all 16 archive types (Note, Link, Article, Image, File, Todo, Plan, Project, Idea, Bookmark, Course, Book, Snippet, Website, Journal, Prompt)
- [x] T038 [P] Create `app/Enums/UserRole.php` — Admin and User backed enum
- [x] T039 [P] Create `app/Enums/ArchiveStatus.php` — status enum values per type (Todo priority, Plan/Project status, etc.) — keep in a single file with multiple enums or separate files
- [x] T040 [P] Create base routing structure: `routes/api.php` with `/api/v1/` prefix, Sanctum auth middleware group, route files for auth/archives/tags/search/dashboard/settings/ai/telegram/admin
- [x] T041 [P] Configure Sanctum in `bootstrap/app.php` (API middleware group, token abilities)
- [x] T042 [P] Configure Livewire in `config/livewire.php` and `bootstrap/app.php` (layout, asset URL)
- [x] T043 [P] Create app layout view `resources/views/layouts/app.blade.php` — Livewire-compatible layout with nav, flash messages, theme class on html tag
- [x] T044 [P] Set up auth scaffolding: Livewire login/register components (standard Laravel auth with Livewire)
- [x] T045 [P] Create `app/Exceptions/Handler.php` — customize JSON error responses for API (403, 404, 422, 429, 500)
- [x] T046 Run `php artisan migrate` and verify all tables created in PostgreSQL

**Checkpoint**: All migrations run cleanly. Models, Enums, base route files, layout view exist. `php artisan test` passes existing tests.

---

## Phase 3: User Story 1 — Account Registration & Authentication (Priority: P1) 🎯 MVP

**Goal**: Users can register (email + Google OAuth), verify email, log in/out, reset password, and receive rate-limited login protection.

**Independent Test**: Visit `/register`, create an account, receive verification email, verify, log in, see dashboard (empty state), log out, log back in.

### Implementation for User Story 1

- [ ] T047 [P] [US1] Create `app/Http/Controllers/Api/V1/AuthController.php` — register, login, googleLogin, logout, user, forgotPassword, resetPassword, resendVerification endpoints per `contracts/api-auth.md`
- [ ] T048 [P] [US1] Create `app/Http/Requests/Auth/RegisterRequest.php` — validate name, email, password, password_confirmation
- [ ] T049 [P] [US1] Create `app/Http/Requests/Auth/LoginRequest.php` — validate email, password
- [ ] T050 [P] [US1] Create `app/Http/Requests/Auth/ForgotPasswordRequest.php` and `ResetPasswordRequest.php`
- [ ] T051 [P] [US1] Create `app/Services/AuthService.php` — register logic (create user, send verification email), login with rate limiting (5 attempts), Google OAuth integration, password reset flows
- [ ] T052 [P] [US1] Create `app/Actions/CreateUserAction.php` — single-responsibility action for user creation with ULID
- [ ] T053 [P] [US1] Create `app/Http/Resources/UserResource.php` — API resource for user responses
- [ ] T054 [US1] Wire API routes in `routes/api.php` under `api/v1/auth/*` — register, login, google, logout, password/*, email/verify/*, user
- [ ] T055 [US1] Implement email verification: create `app/Notifications/VerifyEmailNotification.php` (customizable for Sanctum/Livewire), wire verification routes
- [ ] T056 [US1] Create Livewire auth components in `app/Livewire/Auth/` — Login.php, Register.php, ForgotPassword.php, ResetPassword.php with corresponding views in `resources/views/livewire/auth/`
- [ ] T057 [US1] Implement Google OAuth: create `app/Services/SocialiteService.php`, Socialite controller, routes for redirect/callback
- [ ] T058 [US1] Implement rate limiting in `app/Http/Middleware/LoginRateLimiter.php` — 5 failed attempts, temporary lockout
- [ ] T059 [US1] Create and run seeder `database/seeders/UserSeeder.php` with a test user for development

**Checkpoint**: Full registration, email verification, login/logout, password reset cycles working via both Web UI and API. Rate limiting enforced after 5 failed attempts.

---

## Phase 4: User Story 2 — Archive CRUD Operations (Priority: P1) 🎯 MVP

**Goal**: Users can create, view, list, edit, delete, favorite, and restore all 16 archive types. Type-specific fields are saved/loaded from extension tables.

**Independent Test**: Log in, select an archive type (Note), create entry with title + description, save, verify in list view, edit content, delete with confirmation, restore from trash.

### Implementation for User Story 2

- [ ] T060 [P] [US2] Create `app/Http/Controllers/Api/V1/ArchiveController.php` — index, show, store, update, destroy, restore, forceDelete, toggleFavorite per `contracts/api-archives.md`
- [ ] T061 [P] [US2] Create `app/Http/Requests/Archive/StoreArchiveRequest.php` — validation rules common to all types plus type-specific field validation
- [ ] T062 [P] [US2] Create `app/Http/Requests/Archive/UpdateArchiveRequest.php` — same as store but all fields optional
- [ ] T063 [P] [US2] Create `app/Http/Resources/ArchiveResource.php` — API resource including type-specific fields and tags
- [ ] T064 [P] [US2] Create `app/Services/Archive/ArchiveService.php` — CRUD logic with ownership checks, handles type-specific extension tables
- [ ] T065 [P] [US2] Create `app/Services/Archive/ArchiveFactory.php` — determines which type-specific service/model to use based on archive type
- [ ] T066 [P] [US2] Create `app/Actions/CreateArchiveAction.php` — single-responsibility action for archive creation (base + extension)
- [ ] T067 [P] [US2] Create `app/Actions/UpdateArchiveAction.php` — update base fields + extension table
- [ ] T068 [P] [US2] Create `app/Repositories/ArchiveRepository.php` — query scoping by user, type, favorite, tag filters, pagination
- [ ] T069 [P] [US2] Create `app/DTOs/ArchiveData.php` — type-safe DTO for archive data transfer between layers
- [ ] T070 [US2] Create Livewire archive components in `app/Livewire/Archives/` — ArchiveList.php, ArchiveCreate.php, ArchiveEdit.php, ArchiveShow.php per archive type (start with Note as template, then generalize)
- [ ] T071 [US2] Create Livewire views in `resources/views/livewire/archives/` — type list, create, edit, show templates with Tailwind CSS
- [ ] T072 [US2] Create `app/Http/Controllers/Web/ArchiveController.php` — web routes pointing to Livewire full-page components
- [ ] T073 [US2] Define web routes in `routes/web.php` for archive listing, create, show, edit, delete, favorite toggle, restore
- [ ] T074 [US2] Implement file upload handling in `app/Services/Archive/FileUploadService.php` — validate type/size, store in `storage/app/archives/{user_id}/{type}/{archive_id}.{ext}` per research.md Decision 3
- [ ] T075 [US2] Implement ownership middleware: `app/Http/Middleware/EnsureArchiveOwnership.php` — 403 if not owner
- [ ] T076 [US2] Implement soft-delete restore and permanent delete (30-day cleanup scheduler command in `app/Console/Commands/CleanupTrashedArchives.php`)

**Checkpoint**: All 16 archive types creatable, viewable, listable, editable, deletable via both Web UI and API. Type-specific fields saved correctly. File uploads working.

---

## Phase 5: User Story 3 — Dashboard & Activity Overview (Priority: P1) 🎯 MVP

**Goal**: Authenticated users land on a dashboard showing recent archives, stats by type, recent activity, and quick-action buttons — with proper empty state.

**Independent Test**: Log in with no archives (empty state with prompt), create archives of 3 types, return to dashboard, verify stats, recent list, and quick-action redirects.

### Implementation for User Story 3

- [ ] T077 [P] [US3] Create `app/Http/Controllers/Api/V1/DashboardController.php` — dashboard endpoint per `contracts/api-dashboard.md`
- [ ] T078 [P] [US3] Create `app/Http/Resources/DashboardResource.php` — composite resource for dashboard response
- [ ] T079 [P] [US3] Create `app/Services/DashboardService.php` — aggregate stats (total archives, archives_by_type, favorites_count, tags_count), recent archives (last 10), recent activity (last 10), quick actions (last 3 used types)
- [ ] T080 [US3] Log activity: add model observer `app/Observers/ArchiveObserver.php` — creates ActivityLog entries on archive created/updated/deleted/restored/favorited
- [ ] T081 [US3] Create Livewire dashboard component `app/Livewire/Dashboard.php` — renders dashboard overview
- [ ] T082 [US3] Create dashboard view `resources/views/livewire/dashboard/index.blade.php` — empty state, stats cards, recent archives list, recent activity feed, quick-action buttons with Tailwind CSS
- [ ] T083 [US3] Wire dashboard route: `GET /dashboard` in `routes/web.php` pointing to Livewire Dashboard component

**Checkpoint**: Dashboard loads at `/dashboard` with empty state when no archives exist. Stats, recent archives, and activity update as archives are created. Quick-action buttons redirect to correct create forms.

---

## Phase 6: User Story 4 — Global Tag Management (Priority: P2)

**Goal**: Users can create, rename, delete tags globally. Tags can be assigned to any archive type. Clicking a tag shows all archives across types that have that tag.

**Independent Test**: Create a Note with tags, create a Link with some overlapping tags, navigate to tags page, verify cross-type filtering when clicking a tag.

### Implementation for User Story 4

- [ ] T084 [P] [US4] Create `app/Http/Controllers/Api/V1/TagController.php` — index, store, show, update, delete, archives per `contracts/api-tags.md`
- [ ] T085 [P] [US4] Create `app/Http/Resources/TagResource.php` — tag with archives_count
- [ ] T086 [P] [US4] Create `app/Services/TagService.php` — CRUD with ownership, unique-per-user name validation
- [ ] T087 [US4] Create Livewire tag components: `app/Livewire/Tags/TagManager.php` (list/create/rename/delete), `app/Livewire/Tags/TagSelector.php` (reusable multi-select input for archive forms)
- [ ] T088 [US4] Create Livewire tag views in `resources/views/livewire/tags/` — tag manager UI and tag selector widget
- [ ] T089 [US4] Integrate TagSelector into archive create/edit Livewire forms — users can add/remove tags inline
- [ ] T090 [US4] Create tag filtering endpoint: `GET /tags/{id}/archives` returns all archives (any type) with that tag, paginated
- [ ] T091 [US4] Web route: `GET /tags` and `GET /tags/{id}/archives` in `routes/web.php`

**Checkpoint**: Tags created globally, assignable to any archive, filterable cross-type. Tag selector appears in archive forms. Tag manager page shows all tags with counts.

---

## Phase 7: User Story 5 — Global Search (Priority: P2)

**Goal**: Users can search across all archives using PostgreSQL full-text search. Results grouped by type, with weighted relevance, type/tag/favorite filters, and highlighted snippets.

**Independent Test**: Create archives with distinct content across types, search by keyword matching some, verify grouped results, filter by type, search with no matches and verify helpful empty state.

### Implementation for User Story 5

- [ ] T092 [P] [US5] Create `app/Contracts/SearchEngineInterface.php` — abstraction layer with `search(SearchQuery $query): SearchResult` method (research.md Decision 2)
- [ ] T093 [P] [US5] Create `app/Services/Search/PostgresSearchEngine.php` — implements SearchEngineInterface, uses tsquery/ts_rank with weighted title (A), description (B), tags (C). Results grouped by type with snippets.
- [ ] T094 [P] [US5] Create `app/DTOs/SearchQuery.php` — q, type (optional), tag (optional), favorite (optional), page, perPage
- [ ] T095 [P] [US5] Create `app/DTOs/SearchResult.php` — query, totalResults, groups (type, count, results with id/title/snippet/favorite/tags/score)
- [ ] T096 [P] [US5] Create `app/Http/Controllers/Api/V1/SearchController.php` — search endpoint per `contracts/api-search.md`
- [ ] T097 [P] [US5] Create `app/Http/Resources/SearchResultResource.php`
- [ ] T098 [US5] Create Livewire global search component `app/Livewire/Search/GlobalSearch.php` — real-time search bar with debounced input, grouped results dropdown
- [ ] T099 [US5] Create search view `resources/views/livewire/search/global-search.blade.php` — search bar UI, results grouped by type with snippets
- [ ] T100 [US5] Add database trigger/observer in `app/Observers/ArchiveObserver.php` to update `search_vector` column on title/description/tag changes
- [ ] T101 [US5] Web route for search: `GET /search` in `routes/web.php`

**Checkpoint**: Search returns weighted, grouped, filterable results in < 2s for up to 10k archives per user. Empty state with suggestions when no matches. Abstraction layer allows swapping search engine.

---

## Phase 8: User Story 6 — User Settings & Preferences (Priority: P2)

**Goal**: Users can update profile (name, email, avatar), toggle light/dark/system theme (persisted across sessions), manage API tokens, configure AI provider.

**Independent Test**: Navigate to settings, change name, toggle dark mode, verify persistence across logout/login. Create an API token, verify it appears once. Change AI provider config.

### Implementation for User Story 6

- [ ] T102 [P] [US6] Create `app/Http/Controllers/Api/V1/SettingsController.php` — updateProfile, updatePreferences, tokens (list/create/revoke) per `contracts/api-settings.md`
- [ ] T103 [P] [US6] Create `app/Http/Requests/Settings/UpdateProfileRequest.php`
- [ ] T104 [P] [US6] Create `app/Http/Requests/Settings/UpdatePreferencesRequest.php`
- [ ] T105 [P] [US6] Create `app/Services/SettingsService.php` — profile updates, preference management, Sanctum token CRUD
- [ ] T106 [US6] Create Livewire settings components: `app/Livewire/Settings/Profile.php`, `app/Livewire/Settings/Preferences.php`, `app/Livewire/Settings/ApiTokens.php`
- [ ] T107 [US6] Create settings views in `resources/views/livewire/settings/` — profile form, theme toggle (light/dark/system), API token manager, AI provider config
- [ ] T108 [US6] Implement dark mode: add theme class to `<html>` in layout, persist preference to `user_preferences.theme`, default to system preference via `prefers-color-scheme` media query
- [ ] T109 [US6] Web routes for settings: `GET /settings`, `POST /settings/profile`, `POST /settings/preferences` in `routes/web.php`

**Checkpoint**: Profile updates save. Theme toggle switches entire UI and persists across sessions. API tokens creatable and revocable. Settings page fully functional.

---

## Phase 9: User Story 7 — AI-Assisted Workflows (Priority: P2)

**Goal**: Users can optionally configure an AI provider. When configured: AI suggests archive type/tags, generates summaries, provides global and per-archive chat. All features gracefully degrade when AI is disabled.

**Independent Test**: Configure AI provider, create an archive, request tag suggestions, request summary, use global chat, use archive-scoped chat. Then disable AI provider and verify all AI features show appropriate "not configured" message while CRUD/search still works.

### Implementation for User Story 7

- [ ] T110 [P] [US7] Create `app/Contracts/AiProviderInterface.php` — classify, suggestTags, summarize, chat, retrieve methods per research.md Decision 5
- [ ] T111 [P] [US7] Create `app/Services/AI/DeepSeekProvider.php` — implements AiProviderInterface for DeepSeek (default MVP provider)
- [ ] T112 [P] [US7] Create `app/Services/AI/OpenAiProvider.php` — implements AiProviderInterface for OpenAI
- [ ] T113 [P] [US7] Create `app/Services/AI/AiService.php` — facade that loads configured provider from user preferences, fallback when no provider configured
- [ ] T114 [P] [US7] Create `app/Http/Controllers/Api/V1/AiController.php` — classify, suggestTags, summarize, chat endpoints per `contracts/api-ai.md`
- [ ] T115 [P] [US7] Create `app/Http/Requests/Ai/ChatRequest.php` — validate query, optional archive_id for scoped chat mode
- [ ] T116 [P] [US7] Create `app/Services/AI/ContextBuilder.php` — builds archive context for AI: retrieves relevant archives (global) or single archive (scoped) for chat
- [ ] T117 [US7] Create Livewire AI components: `app/Livewire/Ai/AiChat.php` (dual-mode chat interface), `app/Livewire/Ai/SuggestionPanel.php` (tag suggestions inline)
- [ ] T118 [US7] Create AI views in `resources/views/livewire/ai/` — chat interface with message history, suggestion panel shown on archive create/edit
- [ ] T119 [US7] Add AI suggestion buttons to archive Create/Edit Livewire forms: "Suggest tags" and "Classify type" buttons that call AiService
- [ ] T120 [US7] Create `app/Console/Commands/CleanupAiConversations.php` — scheduled cleanup for old conversations
- [ ] T121 [US7] Wire API routes in `routes/api.php` under `api/v1/ai/*`

**Checkpoint**: AI features work when a provider is configured — tag suggestions, summaries, classification, chat (global + scoped). All features show graceful degradation with "AI not configured" when no provider configured. CRUD/search unaffected.

---

## Phase 10: User Story 8 — Telegram Integration (Priority: P3)

**Goal**: Users can connect their Telegram account, forward messages/links/photos/files to the bot and have them saved as archives. Bot commands for search and AI interaction work when AI is configured.

**Independent Test**: Connect Telegram account via settings, send a text message to the bot, verify Note created in web UI. Forward a URL, verify Link created. Send a photo, verify Image created.

### Implementation for User Story 8

- [ ] T122 [P] [US8] Create `app/Services/Telegram/TelegramService.php` — webhook handler, message-to-archive mapping (text→Note, URL→Link, photo→Image, document→File), bot commands (/search, /recent, /help)
- [ ] T123 [P] [US8] Create `app/Http/Controllers/Api/V1/TelegramController.php` — webhook receiver, connect/disconnect endpoints per `contracts/api-telegram.md`
- [ ] T124 [P] [US8] Create `app/Services/Telegram/TelegramAuthService.php` — connection code generation, user-bot linking via Sanctum token
- [ ] T125 [P] [US8] Create `app/Models/TelegramConnection.php` migration (already in T024) and model (already in T035 — verify created)
- [ ] T126 [US8] Create Livewire Telegram settings component `app/Livewire/Telegram/TelegramSettings.php` — connection status, connect/disconnect button, connection code display
- [ ] T127 [US8] Create settings view `resources/views/livewire/telegram/settings.blade.php` — connection flow UI
- [ ] T128 [US8] Web route for Telegram settings in `routes/web.php` (part of settings page)
- [ ] T129 [US8] Wire API routes for Telegram in `routes/api.php` under `api/v1/telegram/*`

**Checkpoint**: Telegram bot accepts messages and creates correct archive types. Bot responds to /recent and /search commands. Connection can be established and revoked from settings.

---

## Phase 11: User Story 9 — Admin User Management (Priority: P3)

**Goal**: Admin users can view all users, promote/demote roles, and configure system-wide settings. Regular users receive 403 on admin pages.

**Independent Test**: Create two accounts, promote one to admin via seeder, log in as admin, view user list, change role, log in as regular user, verify 403 on admin pages.

### Implementation for User Story 9

- [ ] T130 [P] [US9] Create `app/Http/Controllers/Api/V1/AdminController.php` — listUsers, updateUserRole, systemSettings per `contracts/api-admin.md`
- [ ] T131 [P] [US9] Create `app/Http/Resources/AdminUserResource.php`
- [ ] T132 [P] [US9] Create `app/Http/Middleware/EnsureAdmin.php` — middleware that checks user role
- [ ] T133 [P] [US9] Create `app/Services/AdminService.php` — user listing with pagination, role updates, system settings management
- [ ] T134 [US9] Create Livewire admin components: `app/Livewire/Admin/UserManager.php`, `app/Livewire/Admin/SystemSettings.php`
- [ ] T135 [US9] Create admin views in `resources/views/livewire/admin/` — user list table with role dropdown, system settings form
- [ ] T136 [US9] Wire admin API routes in `routes/api.php` under `api/v1/admin/*` with admin middleware
- [ ] T137 [US9] Wire web admin routes in `routes/web.php` — `/admin/users`, `/admin/settings`
- [ ] T138 [US9] Create `database/seeders/AdminSeeder.php` — creates an initial admin user

**Checkpoint**: Admin interface lists all users, roles changeable. Regular users get 403 on admin routes. System settings (registration open/closed) functional.

---

## Phase 12: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories — validation, error handling, security hardening, documentation, performance.

- [ ] T139 [P] Ensure all Form Requests return consistent JSON error structure (422 with field-level errors) per `contracts/` standards
- [ ] T140 [P] Add `app/Http/Middleware/ForceJsonResponse.php` for API routes — ensures API always returns JSON
- [ ] T141 [P] Security hardening: add CSP headers, X-Frame-Options, rate limiting on all API routes (different limits per endpoint group)
- [ ] T142 [P] Create `database/seeders/DatabaseSeeder.php` that chains all seeders
- [ ] T143 [P] Write README.md with setup instructions (adapt from `quickstart.md`)
- [ ] T144 [P] Add file type and size validation for image/file uploads (10MB images, 25MB files) in `config/filesystems.php`
- [ ] T145 [P] Create scheduled task in `routes/console.php`: daily cleanup of 30-day old trashed archives
- [ ] T146 [P] Verify all Blade views respect dark mode via `dark:` Tailwind classes
- [ ] T147 [P] Verify all Livewire components handle loading, empty, error states
- [ ] T148 [P] Run `composer run test` and confirm all tests pass
- [ ] T149 [P] Run `./vendor/bin/pint` for code style consistency
- [ ] T150 [P] Run through `quickstart.md` validation scenarios end-to-end

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies
- **Foundational (Phase 2)**: Depends on Setup — BLOCKS all user stories
- **US1 — Auth (Phase 3)**: Depends on Foundational — BLOCKS US3 (Dashboard) but not US2
- **US2 — Archives (Phase 4)**: Depends on Foundational — independent of US1 for API (auth required for testing)
- **US3 — Dashboard (Phase 5)**: Depends on US1 (needs auth) and US2 (needs archives to display)
- **US4 — Tags (Phase 6)**: Depends on US2 (needs archives to tag) — can run in parallel with US5/US6
- **US5 — Search (Phase 7)**: Depends on US2 (needs archives to search) — can run in parallel with US4/US6
- **US6 — Settings (Phase 8)**: Depends on US1 (needs auth) — independent of US2-US5
- **US7 — AI (Phase 9)**: Depends on US2 (needs archives), US6 (needs AI provider config) — independent otherwise
- **US8 — Telegram (Phase 10)**: Depends on US1 (needs auth), US2 (needs archives), partially US7 for AI commands
- **US9 — Admin (Phase 11)**: Depends on US1 (needs auth + roles) — independent of archive/tag/search stories
- **Polish (Phase 12)**: Depends on all desired user stories

### User Story Dependency Diagram

```
                        ┌──────────┐
                        │ Phase 1  │
                        │  Setup   │
                        └────┬─────┘
                             │
                        ┌────▼─────┐
                        │ Phase 2  │
                        │ Found.   │
                        └────┬─────┘
                             │
           ┌─────────────────┼──────────────────┐
           │                 │                  │
     ┌─────▼──────┐   ┌─────▼──────┐    ┌──────▼──────┐
     │ Phase 3    │   │ Phase 4    │    │ Phase 8     │
     │ US1 Auth   │   │ US2 Arch. │    │ US6 Settings│
     └─────┬──────┘   └──────┬─────┘    └──────┬──────┘
           │                 │                  │
           │          ┌──────┼──────┐           │
           │          │      │      │           │
     ┌─────▼──────┐ ┌─▼──┐ ┌─▼──┐ ┌─▼──┐ ┌────▼──────┐
     │ Phase 5    │ │P6  │ │P7  │ │P9  │ │ Phase 10  │
     │ US3 Dash   │ │US4 │ │US5 │ │US7 │ │ US8 Tel.  │
     └─────┬──────┘ │Tags│ │Srch│ │ AI │ └─────┬──────┘
           │        └────┘ └────┘ └────┘       │
           │          │      │      │           │
           └──────────┴──────┴──────┴───────────┘
                                     │
                              ┌──────▼──────┐
                              │ Phase 11    │
                              │ US9 Admin   │
                              └──────┬──────┘
                                     │
                              ┌──────▼──────┐
                              │ Phase 12    │
                              │ Polish      │
                              └─────────────┘
```

### Within Each User Story

- Models → Services → Controllers → Routes → Livewire Components → Views
- API endpoints first (contract-first), then Livewire web UI

### Parallel Opportunities

| Scope | Tasks |
|-------|-------|
| Phase 1 Setup | T002, T003 (package installs) can run together |
| Phase 2 Migrations | T008-T026 (all migration creation tasks) fully parallel |
| Phase 2 Models | T029-T039 (all model/enum creation tasks) fully parallel |
| All Phases API Tasks | All `[P]` tasks within any phase — different files, no dependencies |
| US4, US5, US6 | Can be implemented in parallel (different services, no shared files beyond Archive model) |
| US7, US9 | Can be implemented in parallel with each other and with US4-US6 |

---

## Implementation Strategy

### MVP First (Phases 1-5)

1. Complete Phase 1: Setup — install packages
2. Complete Phase 2: Foundational — database, models, routing, auth scaffolding
3. Complete Phase 3: US1 — Auth (register, login, verify, reset password)
4. Complete Phase 4: US2 — Archive CRUD (all 16 types)
5. Complete Phase 5: US3 — Dashboard & Activity
6. **STOP and VALIDATE**: Full MVP — user can register, log in, create archives, see dashboard
7. Deploy if ready

### Incremental Delivery

1. Setup + Foundational → Foundation ready (db schema, models, API skeleton)
2. US1 Auth → Users can register and log in
3. US2 Archives → Core value proposition functional
4. US3 Dashboard → Cohesive landing experience (MVP complete!)
5. US4 Tags → Organization
6. US5 Search → Retrieval
7. US6 Settings → Customization
8. US7 AI → Enhancement layer
9. US8 Telegram → Mobile integration
10. US9 Admin → Platform management
11. Polish → Production readiness

### Parallel Team Strategy

With multiple developers:

1. Team completes Setup + Foundational together
2. Once Foundational is done:
   - Developer A: US1 Auth → US3 Dashboard
   - Developer B: US2 Archives → US4 Tags → US5 Search
   - Developer C: US6 Settings → US7 AI
3. Each story produces an independently testable increment

---

## Notes

- `[P]` tasks = different files, no dependencies — can run in parallel
- `[US1]`–`[US9]` labels map tasks to specific user stories
- Each phase checkpoint should be validated before proceeding
- Commit after each task or logical group
- All API contracts defined in `specs/001-initial-project-spec/contracts/` are authoritative for request/response shapes
- The project already has Laravel 13, Vite, Tailwind v4, and Pest installed — Phase 1 focuses on adding missing packages
- Data model design is in `specs/001-initial-project-spec/data-model.md` — all migrations derive from it
- ULID is required for all entities per constitution and plan.md
