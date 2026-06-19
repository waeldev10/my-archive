# Modules

The project uses a domain-based modular architecture. Each module is self-contained
with its own classes, views, routes, and tests. All modules live under `Modules/`.

---

## Core

**Shared infrastructure** — not a feature module. Provides everything other modules
need to stay lean and consistent.

### Responsibilities

- Shared contracts/interfaces (`Core/Contracts/`)
- Enums (`Core/Enums/`)
- Exceptions and error handling (`Core/Exceptions/`)
- Helper/utility functions (`Core/Helpers/`)
- Service Providers (`Core/Providers/`)
- Traits (e.g., `UsesUlid`) (`Core/Traits/`)
- Support/base classes (`Core/Support/`)
- Reusable Livewire components (`Core/Livewire/Components/`)
- Application layouts (`Core/View/Layouts/`)
- Shared Blade components (`Core/View/Components/`)

### Rules

- Feature modules MUST NOT duplicate shared infrastructure — use Core instead
- Feature modules MUST pull shared dependencies from Core, not duplicate them

---

## Auth

Responsible for all authentication and user management.

### Responsibilities

- Email/password registration with email verification
- Google OAuth login
- Login/logout with rate limiting
- Password reset
- User profile management

### Typical Structure

```
Auth/
├── Actions/
├── DTOs/
├── Events/
├── Http/
│   ├── Controllers/Api/
│   ├── Controllers/Web/
│   ├── Middleware/          (e.g., LoginRateLimiter)
│   └── Requests/
├── Listeners/
├── Livewire/                (login, register, password reset components)
├── Models/                  (User, UserPreference)
├── Notifications/           (verify email, password reset)
├── Providers/
├── Repositories/
├── Services/
└── Views/
```

---

## Archives

Responsible for all 16 archive types. This is the core value proposition of the platform.

### Archive Types

- Note, Link, Article, Image, File
- Todo, Plan, Project, Idea, Bookmark
- Course, Book, Snippet, Website, Journal
- Prompt

### Responsibilities

- CRUD for all archive types (Create, Read, Update, Delete, soft-delete)
- Type-specific field handling via extension tables
- File upload and storage
- Favorites and trash management
- Ownership enforcement

### Rules

- All 16 archive types stay inside this module — no sub-modules per type
- Shared archive logic lives in base classes within this module

### Typical Structure

```
Archives/
├── Actions/
├── DTOs/
├── Events/
├── Http/
│   ├── Controllers/Api/
│   ├── Controllers/Web/
│   └── Requests/
├── Listeners/
├── Livewire/                (list, create, edit, show per type)
├── Models/                  (Archive, ArchiveLink, ArchiveImage, etc.)
├── Policies/                (ownership)
├── Repositories/
├── Services/                (including FileUploadService, ArchiveFactory)
└── Views/
```

---

## Tags

Responsible for global tag management across all archive types.

### Responsibilities

- Create, rename, delete tags
- Assign/remove tags on any archive type
- Cross-type tag filtering
- Tag color and display

### Typical Structure

```
Tags/
├── Actions/
├── DTOs/
├── Http/
│   ├── Controllers/Api/
│   ├── Controllers/Web/
│   └── Requests/
├── Livewire/                (TagManager, TagSelector components)
├── Models/                  (Tag, Taggable pivot)
├── Repositories/
├── Services/
└── Views/
```

---

## Search

Responsible for global full-text search across all archives.

### Responsibilities

- PostgreSQL full-text search (tsvector/tsquery)
- Weighted relevance ranking (title > description > tags)
- Grouped results by archive type
- Filtering by type, tag, favorite status
- Search abstraction layer (`SearchEngineInterface`)

### Typical Structure

```
Search/
├── Contracts/               (SearchEngineInterface)
├── DTOs/                    (SearchQuery, SearchResult)
├── Http/
│   └── Controllers/Api/
├── Livewire/                (GlobalSearch component)
├── Repositories/
├── Services/                (PostgresSearchEngine)
└── Views/
```

---

## AI

Responsible for the AI enhancement layer. All features are strictly optional.

### Responsibilities

- Archive type classification
- Tag suggestions
- Content summarization
- Dual-mode AI chat (global + archive-scoped)
- Knowledge retrieval across archives
- AI provider abstraction (`AiProviderInterface`)

### Rules

- All AI features MUST gracefully degrade when no provider is configured
- The platform MUST remain fully functional without AI

### Typical Structure

```
AI/
├── Actions/
├── Contracts/               (AiProviderInterface)
├── DTOs/
├── Events/
├── Http/
│   ├── Controllers/Api/
│   └── Requests/
├── Livewire/                (AiChat, SuggestionPanel)
├── Models/                  (AiConversation, AiMessage)
├── Repositories/
├── Services/                (AiService, ContextBuilder, DeepSeekProvider, OpenAiProvider)
└── Views/
```

---

## Telegram

Responsible for Telegram bot integration.

### Responsibilities

- Telegram bot webhook handling
- Message-to-archive mapping (text → Note, URL → Link, photo → Image, file → File)
- Bot commands (/search, /recent, /help)
- Account connection/disconnection
- AI interaction through Telegram

### Typical Structure

```
Telegram/
├── Actions/
├── DTOs/
├── Events/
├── Http/
│   ├── Controllers/Api/
│   └── Requests/
├── Livewire/                (TelegramSettings component)
├── Models/                  (TelegramConnection)
├── Repositories/
├── Services/                (TelegramService, TelegramAuthService)
└── Views/
```

---

## Dashboard

Responsible for the landing page overview.

### Responsibilities

- Recent archives list (last 10)
- Archive statistics (total count, breakdown by type)
- Recent activity feed
- Quick-action buttons (recently used types)
- Empty state with "Create your first archive" prompt

### Typical Structure

```
Dashboard/
├── Actions/
├── DTOs/
├── Http/
│   ├── Controllers/Api/
│   ├── Controllers/Web/
│   └── Resources/
├── Livewire/                (Dashboard component)
├── Models/                  (ActivityLog — may live here or in Archives)
├── Repositories/
├── Services/
└── Views/
```

---

## Settings

Responsible for user and application settings.

### Responsibilities

- Profile updates (name, email, avatar)
- Theme preferences (light/dark/system)
- API token management (create, list, revoke)
- AI provider configuration
- Notification preferences

### Typical Structure

```
Settings/
├── Actions/
├── DTOs/
├── Http/
│   ├── Controllers/Api/
│   ├── Controllers/Web/
│   └── Requests/
├── Livewire/                (Profile, Preferences, ApiTokens components)
├── Repositories/
├── Services/
└── Views/
```

---

## Admin

Responsible for platform administration.

### Responsibilities

- User listing with pagination and filters
- Role management (promote/demote between Admin and User)
- System-wide settings (registration open/closed, default AI provider)

### Typical Structure

```
Admin/
├── Actions/
├── DTOs/
├── Http/
│   ├── Controllers/Api/
│   ├── Controllers/Web/
│   ├── Middleware/           (EnsureAdmin)
│   └── Requests/
├── Livewire/                (UserManager, SystemSettings)
├── Repositories/
├── Services/
└── Views/
```

---

## Cross-Cutting Concerns

These are NOT modules but span across all modules:

| Concern | Implementation |
|---------|---------------|
| **API** | Each module exposes its own API endpoints under `Http/Controllers/Api/`. Routes registered in `routes/api.php`. |
| **Routes** | Defined in `routes/web.php` and `routes/api.php`. May delegate to module-specific route files. |
| **Config** | Standard Laravel config in `config/`. |
| **Database** | Migrations in `database/migrations/`. Seeders in `database/seeders/`. |
| **Tests** | Feature tests in `tests/Feature/`. Unit tests in `tests/Unit/`. May also live inside modules. |
