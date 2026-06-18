# Feature Specification: Initial Platform — My Archive

**Feature Branch**: `001-initial-project-spec`

**Created**: 2026-06-16

**Status**: Draft

**Input**: Complete project specification for the My Archive personal knowledge
and archive management platform.

## Clarifications

### Session 2026-06-16

- **Q**: How should the 16 archive types be structured in terms of their field
  schemas? → **A**: Hybrid approach — a shared `archives` table for common
  fields (id/ULID, user_id, type, title, description, favorite state,
  timestamps) plus type-specific extension tables for types that require
  dedicated fields. This provides normalization, extensibility, and strong
  typing without relying heavily on JSON fields. Each type has its own CRUD
  interfaces and business rules while sharing common archive attributes.
- **Q**: How should the Search module be implemented? → **A**: PostgreSQL
  full-text search (tsvector/tsquery) with weighted relevance ranking (title
  highest, content medium, tags contribute). Results grouped by archive type
  with filters by type and tags. The Search module MUST be designed behind an
  abstraction layer so external search engines (Meilisearch, Typesense) can
  replace PostgreSQL FTS later without changing business logic or APIs.
  External search engines are not required for MVP.
- **Q**: What is the scope of the AI chat interface? → **A**: Dual-mode chat
  — both global knowledge chat and per-archive context chat. Global mode
  (default) retrieves and synthesizes answers across all user archives.
  Archive mode is activated when the user starts the chat from a specific
  archive and scopes answers to that archive's content only. The same chat
  interface supports both modes through optional archive scoping.
- **Q**: Where and how should uploaded files be stored? → **A**: Local
  filesystem behind Laravel's filesystem abstraction for MVP. Files stored
  privately in `storage/app/archives/` and served through the application
  with authentication and authorization checks. The abstraction layer allows
  migration to S3-compatible storage through configuration changes only,
  without modifying business logic.
- **Q**: What is the API authentication model and versioning strategy? → **A**:
  URL prefix versioning (`/api/v1/`) with Laravel Sanctum — personal access
  tokens for external clients (Telegram bot, browser extensions, mobile apps)
  and session authentication for the web SPA. The auth architecture SHOULD
  support future adoption of OAuth2/Passport if third-party developer
  integrations become necessary. OAuth2 is not required for MVP.

## User Scenarios & Testing

### User Story 1 — Account Registration & Authentication (Priority: P1)

A new user discovers My Archive and wants to create an account. They sign up
using their email address or Google account, verify their email, and log in
to access the platform. Returning users authenticate and land on their
dashboard. If they forget their password, they reset it through a secure
email flow.

**Why this priority**: Authentication is the foundation of the entire platform
— no feature is accessible without it. Every other user story depends on the
user being authenticated.

**Independent Test**: Can be fully tested by visiting the registration page,
creating an account with email, receiving the verification email, verifying,
and logging in. The user then sees the dashboard (empty state) and can log
out and back in.

**Acceptance Scenarios**:

1. **Given** a new visitor, **When** they register with a valid email and
   password, **Then** an account is created, a verification email is sent,
   and they are prompted to verify their email before proceeding.
2. **Given** a new visitor, **When** they register with Google OAuth,
   **Then** an account is created without email verification, and they are
   logged in immediately.
3. **Given** a registered user, **When** they log in with correct credentials,
   **Then** they are redirected to the dashboard.
4. **Given** a registered user, **When** they enter incorrect credentials
   repeatedly, **Then** they are temporarily locked out after 5 failed
   attempts with a rate-limiting message.
5. **Given** a registered user, **When** they request a password reset,
   **Then** they receive an email with a secure reset link that expires
   after 60 minutes.
6. **Given** an unverified user, **When** they try to access the dashboard,
   **Then** they are shown a prompt to verify their email with an option
   to resend the verification.

---

### User Story 2 — Archive CRUD Operations (Priority: P1)

An authenticated user wants to save information. They navigate to any of the
16 supported archive types (Note, Link, Article, Image, File, Todo, Plan,
Project, Idea, Bookmark, Course, Book, Snippet, Website, Journal, Prompt),
create a new entry, fill in type-specific fields, save it, and see it appear
in their archives. They can view, edit, and delete any archive they own.

**Why this priority**: Archive creation and management is the core value
proposition of the platform. Without this, the platform serves no purpose.

**Independent Test**: Can be fully tested by logging in, selecting an archive
type (e.g., Note), creating a new entry with a title and body, saving,
confirming it appears in the archive list, editing the content, and deleting
it. This can be done entirely through CRUD without any AI or external
integration.

**Acceptance Scenarios**:

1. **Given** an authenticated user, **When** they navigate to any archive
   type page, **Then** they see a list of existing archives of that type
   (or an empty state with a "Create" call-to-action).
2. **Given** an authenticated user on a create form, **When** they fill in
   the required fields and submit, **Then** the archive is created, they
   are redirected to the detail view, and a success message is shown.
3. **Given** an authenticated user viewing an archive, **When** they click
   "Edit", **Then** they see a pre-filled form and can update any field.
4. **Given** an authenticated user viewing an archive, **When** they delete
   it, **Then** they are asked to confirm, the archive is removed, and they
   are returned to the list with a success message.
5. **Given** an authenticated user, **When** they try to access another
   user's archive directly, **Then** they receive a 403 Forbidden response.
6. **Given** an authenticated user, **When** they submit an archive creation
   form with invalid data, **Then** they see inline validation errors and
   the form preserves their entered values.

---

### User Story 3 — Dashboard & Activity Overview (Priority: P1)

An authenticated user logs in and lands on their dashboard. They see recent
archives they've created, quick stats (total archives per type, recent
activity), and quick-action buttons to create a new archive of any type.

**Why this priority**: The dashboard is the default landing page and the
primary navigation hub. Without it, users have no clear starting point.

**Independent Test**: Can be fully tested by logging in, creating a few
archives of different types, and verifying the dashboard updates to show
recent archives and statistics. Test with zero archives to verify empty
state.

**Acceptance Scenarios**:

1. **Given** an authenticated user with no archives, **When** they land on
   the dashboard, **Then** they see an empty state with a "Create your first
   archive" prompt and quick-action buttons.
2. **Given** an authenticated user with existing archives, **When** they
   land on the dashboard, **Then** they see recent archives (last 10), total
   archive count, breakdown by type, and recent activity timestamps.
3. **Given** an authenticated user on the dashboard, **When** they click a
   quick-action button (e.g., "New Note"), **Then** they are taken directly
   to the create form for that archive type.

---

### User Story 4 — Global Tag Management (Priority: P2)

An authenticated user wants to organize their archives by topic. They create
tags (e.g., "Laravel", "AI", "Business"), assign them to archives of any
type, and filter their archives by tag. Tags are global — the same tag can
be used across Notes, Links, Articles, and any other type.

**Why this priority**: Tags are the primary organization mechanism. They
provide cross-type discoverability that folders alone cannot.

**Independent Test**: Can be fully tested by creating notes and links,
creating tags, assigning tags to archives of different types, and then
filtering to see all archives with a specific tag.

**Acceptance Scenarios**:

1. **Given** an authenticated user, **When** they create a tag with a name,
   **Then** the tag is available globally and can be assigned to any archive
   type.
2. **Given** an authenticated user editing an archive, **When** they add or
   remove tags, **Then** the changes are saved and reflected immediately.
3. **Given** an authenticated user, **When** they click on a tag from any
   archive, **Then** they see all archives tagged with that tag, across all
   types.
4. **Given** an authenticated user, **When** they delete a tag, **Then** the
   tag is removed from all archives it was assigned to.

---

### User Story 5 — Global Search (Priority: P2)

An authenticated user wants to find specific information across all their
archives. They use the global search bar to search by title, content, tags,
or type. Results are displayed grouped by archive type with relevant snippets.

**Why this priority**: Search is the primary retrieval mechanism. As archive
count grows, manual browsing becomes impractical.

**Independent Test**: Can be fully tested by creating several archives with
distinct titles and content across different types, then using search to find
them by keyword, tag, and type filter.

**Acceptance Scenarios**:

1. **Given** an authenticated user, **When** they type in the global search
   bar, **Then** they see real-time results grouped by archive type with
   title, snippet, and tag previews.
2. **Given** an authenticated user, **When** they search for a term that
   matches multiple archive types, **Then** results are grouped by type with
   counts per group.
3. **Given** an authenticated user, **When** they search with no results,
   **Then** they see a helpful empty state with suggestions or an option to
   create a new archive.
4. **Given** an authenticated user, **When** they filter search by archive
   type, **Then** results are limited to that type only.
5. **Given** an authenticated user, **When** they search by tag name,
   **Then** results include all archives tagged with that tag.

---

### User Story 6 — User Settings & Preferences (Priority: P2)

An authenticated user customizes their experience. They update their profile
(name, email, avatar), switch between light and dark themes, and configure
application preferences (language, notification settings, AI provider).

**Why this priority**: Settings enhance usability and give users control over
their experience. Dark mode is a baseline expectation for modern applications.

**Independent Test**: Can be fully tested by navigating to settings,
changing the theme to dark mode, updating profile information, and verifying
the changes persist across sessions.

**Acceptance Scenarios**:

1. **Given** an authenticated user, **When** they update their profile
   (name, email), **Then** the changes are saved and reflected immediately.
2. **Given** an authenticated user, **When** they toggle dark mode, **Then**
   the entire interface switches to dark theme immediately and persists the
   preference.
3. **Given** an authenticated user, **When** they change their theme
   preference, **Then** new sessions respect the saved preference.
4. **Given** an authenticated user, **When** they update their AI provider
   configuration, **Then** AI features use the selected provider.

---

### User Story 7 — AI-Assisted Workflows (Priority: P2)

An authenticated user leverages AI to enhance their archive management. They
use AI to automatically classify archives into the correct type, suggest
relevant tags, generate summaries of long content, ask questions about their
archives through an AI chat interface, and retrieve knowledge across their
entire archive collection. All AI features are optional — the platform
remains fully functional without AI provider access.

**Why this priority**: AI features are enhancement only per the constitutional
principle (III). Core CRUD and search must work independently. AI features
differentiate the platform but are not required for MVP launch.

**Independent Test**: Can be tested by configuring an AI provider, creating
an archive, requesting tag suggestions and a summary, and verifying the AI
responses appear. Also test by disabling the AI provider to verify the
platform remains fully functional.

**Acceptance Scenarios**:

1. **Given** an authenticated user with AI configured, **When** they create
   or edit an archive, **Then** they can request AI-generated tag suggestions
   and select from the suggestions.
2. **Given** an authenticated user with AI configured, **When** they view
   an archive with substantial text content, **Then** they can request a
   summary generated by AI.
3. **Given** an authenticated user with AI configured, **When** they open the
   global AI chat, **Then** they can ask natural language questions about
   their entire archive and receive synthesized answers using relevant
   archives.
4. **Given** an authenticated user with AI configured, **When** they create
   an archive with ambiguous content, **Then** AI can suggest the most
   appropriate archive type for classification.
5. **Given** an authenticated user with AI configured, **When** they enter
   the AI chat from an archive detail view, **Then** the chat is scoped to
   that archive only and will not retrieve content from other archives.
6. **Given** an authenticated user, **When** no AI provider is configured,
   **Then** all AI features show a clear message explaining "AI not
   configured" and the rest of the platform functions normally.

---

### User Story 8 — Telegram Integration (Priority: P3)

An authenticated user connects their Telegram account and forwards messages,
links, and media directly to My Archive from Telegram. They interact with a
Telegram bot to create archives, search their collection, and optionally use
AI features through chat commands.

**Why this priority**: Telegram integration is a key differentiator and part
of the V1 scope per architecture decisions. It enables frictionless archiving
from mobile devices without opening the web interface.

**Independent Test**: Can be tested by connecting a Telegram account,
sending a message to the bot (e.g., a link or note), and verifying it
appears as an archive in the web interface with the correct type.

**Acceptance Scenarios**:

1. **Given** an authenticated user, **When** they connect their Telegram
   account through settings, **Then** they receive a confirmation from the
   bot and can start sending content.
2. **Given** a connected user, **When** they forward a link to the bot,
   **Then** a Link archive is created with the URL, title, and preview.
3. **Given** a connected user, **When** they send a text message to the bot,
   **Then** a Note archive is created with the message content.
4. **Given** a connected user, **When** they send a photo to the bot,
   **Then** an Image archive is created with the photo and caption.
5. **Given** a connected user, **When** they send a file to the bot,
   **Then** a File archive is created with the file attached.
6. **Given** a connected user, **When** they send a command to the bot to
   search archives, **Then** the bot responds with matching results.
7. **Given** a connected user with AI configured, **When** they ask the bot
   to summarize or classify an archive, **Then** the AI processes it and
   returns the result via the bot.

---

### User Story 9 — Admin User Management (Priority: P3)

An admin user logs in and manages the platform. They view all registered
users, can promote users to admin or demote them to regular user, and manage
system-wide settings.

**Why this priority**: Admin functionality is needed for platform maintenance
but is not user-facing for the primary audience. It can be deferred until
multi-user usage requires moderation.

**Independent Test**: Can be tested by creating two accounts (one admin,
one user), logging in as admin, viewing the user list, changing the user's
role, and verifying the changes take effect.

**Acceptance Scenarios**:

1. **Given** an admin user, **When** they navigate to user management,
   **Then** they see a list of all registered users with their roles,
   status, and registration date.
2. **Given** an admin user, **When** they change a user's role from User to
   Admin, **Then** the user gains admin privileges on next login.
3. **Given** an admin user, **When** they view system settings, **Then**
   they can configure application-wide settings (registration open/closed,
   default AI provider, etc.).
4. **Given** a non-admin user, **When** they try to access admin pages,
   **Then** they receive a 403 Forbidden response.

---

### Edge Cases

- What happens when a user registers with an email that already exists?
  The system displays "Email already registered" and offers login or password
  reset options.

- How does the system handle uploading files that exceed size limits?
  The system rejects the upload with a clear error message indicating the
  maximum allowed file size.

- What happens when the AI provider returns an error or times out?
  The AI feature shows a user-friendly error ("AI service unavailable, please
  try again later") and the core CRUD functionality continues unaffected.

- How does the system handle a Telegram bot token that has been revoked?
  The Telegram integration shows a "disconnected" state in settings and
  prompts the user to reconnect.

- What happens when a search query contains special characters or is
  extremely long? Input is sanitized, and queries exceeding a reasonable
  length are truncated with a notification.

- How does an unauthenticated user access the platform?
  They are restricted to the login, registration, and password reset pages
  only. All other routes redirect to login.

## Requirements

### Functional Requirements

- **FR-001**: System MUST allow users to register with email and password,
  including email verification via a confirmation link.
- **FR-002**: System MUST allow users to register and log in using Google
  OAuth without requiring email verification.
- **FR-003**: System MUST authenticate returning users via email/password
  or Google OAuth and redirect them to the dashboard.
- **FR-004**: System MUST provide password reset functionality via a
  time-limited email link (60-minute expiry).
- **FR-005**: System MUST enforce rate limiting on login attempts (5 failed
  attempts before temporary lockout).
- **FR-006**: System MUST provide CRUD operations (Create, Read, Update,
  Delete) for all 16 archive types: Note, Link, Article, Image, File, Todo,
  Plan, Project, Idea, Bookmark, Course, Book, Snippet, Website, Journal,
  Prompt.
- **FR-007**: Archive storage MUST follow a hybrid model: a shared `archives`
  table with common fields (id/ULID, user_id, type, title, description,
  favorite state, timestamps) plus type-specific extension tables for types
  requiring dedicated fields (e.g., URL for Links, file path for Files, code
  block for Snippets, date ranges for Todo/Plan/Project).
- **FR-008**: System MUST provide a listing view for each archive type with
  sorting (by date, title, type) and pagination.
- **FR-009**: System MUST provide a detail view for each archive type
  showing all fields and associated tags.
- **FR-010**: System MUST enforce ownership — users can only view, edit,
  and delete their own archives. Admins can view and manage all archives.
- **FR-011**: System MUST provide a dashboard landing page with recent
  archives, statistics (total count by type), and quick-action buttons for
  creating new archives.
- **FR-012**: System MUST provide a global tag system where tags are shared
  across all archive types.
- **FR-013**: System MUST allow users to create, rename, and delete tags.
- **FR-014**: System MUST allow users to assign and remove tags on any
  archive during create and edit operations.
- **FR-015**: System MUST provide tag-based filtering — clicking a tag shows
  all archives (across types) that have that tag.
- **FR-016**: System MUST provide global search using PostgreSQL full-text
  search (tsvector/tsquery) searching title, description, content, and tags
  with weighted relevance ranking.
- **FR-017**: System MUST group search results by archive type and show
  relevant content snippets.
- **FR-018**: System MUST allow filtering search results by archive type.
- **FR-019**: The Search module MUST be implemented behind an abstraction
  layer (interface/contract) so the underlying search engine (PostgreSQL FTS
  for MVP) can be replaced with dedicated engines (Meilisearch, Typesense)
  without modifying business logic or API behavior.
- **FR-020**: System MUST support light and dark themes with system
  preference detection and manual toggle.
- **FR-021**: System MUST persist user theme and UI preferences across
  sessions.
- **FR-022**: System MUST allow users to update their profile (name, email,
  avatar).
- **FR-023**: System MUST allow users to configure their preferred AI
  provider and API key in settings.
- **FR-024**: System MUST provide AI archive classification — suggesting the
  most appropriate archive type for content being saved.
- **FR-025**: System MUST provide AI tag suggestions — generating relevant
  tags based on archive content.
- **FR-026**: System MUST provide AI summarization — generating a concise
  summary of archive content on demand.
- **FR-027**: System MUST provide a dual-mode AI chat interface where users
  can ask natural language questions: (a) global mode — retrieving and
  synthesizing answers across all user archives, and (b) archive context
  mode — scoping the conversation to a single specific archive. Archive mode
  is activated when the chat is started from an archive detail view.
- **FR-028**: System MUST provide AI knowledge retrieval — searching and
  synthesizing information across all user archives.
- **FR-029**: All AI features MUST be optional and the platform MUST remain
  fully functional when no AI provider is configured.
- **FR-030**: System MUST provide a Telegram bot integration allowing users
  to connect their Telegram account.
- **FR-031**: Telegram bot MUST support creating archives by forwarding
  messages (links → Link, text → Note, photos → Image, files → File).
- **FR-032**: Telegram bot MUST support searching archives and returning
  results via chat message.
- **FR-033**: Telegram bot MUST support AI-powered interactions (summarize,
  classify, ask questions) when AI is configured.
- **FR-034**: System MUST support two user roles: Admin and User.
- **FR-035**: Admin users MUST have access to a user management interface
  listing all users with their roles and status.
- **FR-036**: Admin users MUST be able to promote/demote users between
  Admin and User roles.
- **FR-037**: Admin users MUST have access to system-wide settings
  (registration open/closed, default AI provider).
- **FR-038**: System MUST validate all user input on both client and server
  side, showing inline validation errors.
- **FR-039**: System MUST handle all error states gracefully with
  user-friendly messages and appropriate HTTP status codes.
- **FR-040**: System MUST expose a REST API at `/api/v1/*` from the first
  release, covering all business capabilities.
- **FR-041**: API authentication MUST use Laravel Sanctum — personal access
  tokens for external clients, session-based auth for the web application.
- **FR-042**: System MUST allow users to generate, revoke, and manage
  personal API tokens from their settings page.
- **FR-043**: API consumers expected for V1 include the Telegram bot, browser
  extensions, and mobile applications — each using personal access tokens.

### Key Entities

- **User**: Represents a platform user with authentication credentials
  (email/password or Google OAuth), role (Admin or User), profile
  information (name, avatar), and preferences (theme, AI provider config).
  Each user owns their archives and settings.

- **Archive**: The core content entity with 16 specializations (Note, Link,
  Article, Image, File, Todo, Plan, Project, Idea, Bookmark, Course, Book,
  Snippet, Website, Journal, Prompt). Each archive shares common attributes
  stored in a base table (id, user_id, type, title, description, favorite
  state, timestamps) while type-specific fields are stored in per-type
  extension tables. Archives are owned by a single user and support global
  tagging. Each type has dedicated CRUD, business rules, and views.

- **Tag**: A global label that can be attached to any archive type. Tags
  are created and managed by users and provide cross-type organization and
  filtering. Tags have a name and optional color/icon.

- **User Preference**: Stores user-specific application settings including
  theme choice (light/dark/system), AI provider selection and API key, and
  UI preferences.

- **Activity Log**: Records user actions (archive created, updated, deleted)
  for display on the dashboard activity feed. Associated with a user and
  optionally linked to the affected archive.

- **API Token**: Personal access token issued via Laravel Sanctum for
  authenticating external clients (Telegram bot, browser extensions, mobile
  apps). Users manage tokens from their settings — creating, listing, and
  revoking them. Tokents are hashed in storage and only shown once at
  creation time.

## Success Criteria

### Measurable Outcomes

- **SC-001**: A new user can complete registration and access the dashboard
  in under 2 minutes using email, or under 30 seconds using Google OAuth.
- **SC-002**: An authenticated user can create an archive (any type) in
  under 30 seconds including filling type-specific fields.
- **SC-003**: Global search returns results within 2 seconds for a
  collection of up to 10,000 archives per user.
- **SC-004**: All 16 archive types are independently creatable, viewable,
  editable, and deletable through both the web interface and REST API.
- **SC-005**: The platform operates fully with zero AI provider
  configuration — all CRUD, search, tags, and settings functions work
  without AI.
- **SC-006**: A Telegram user can forward a message and have it appear as
  an archive in their collection within 10 seconds.
- **SC-007**: An admin can view, promote, and demote users through the
  admin interface in under 1 minute.
- **SC-008**: Theme preference (light/dark) persists across sessions and
  the entire UI respects the selected theme with no unreadable contrast
  issues.
- **SC-009**: Rate limiting correctly blocks after 5 failed login attempts
  and unlocks after the configured cooldown period.
- **SC-010**: A user with 100+ archives across multiple types can navigate
  between type-specific listing pages and filter by tag in under 2 seconds
  per page load.

## Assumptions

- Users have stable internet connectivity for web interface and Telegram
  integration usage.
- AI features require the user to configure their own AI provider API key
  — the platform does not include a bundled AI service.
- Users manage their own Telegram bot token — the platform provides setup
  instructions but does not auto-provision bots.
- File uploads are limited to reasonable sizes (10 MB for images, 25 MB for
  other files) — no streaming or chunked uploads for MVP.
- The initial release targets single-user and small-team deployments —
  horizontal scaling is not a V1 concern.
- Uploaded files are stored on the local filesystem behind Laravel's
  filesystem abstraction layer for MVP. Files are private and served through
  the application with authentication checks. The abstraction design allows
  migration to S3-compatible storage via configuration change without
  modifying business logic.
- Email delivery relies on the server's configured mail system (SMTP) —
  no bundled email service.
- Mobile-responsive web design is sufficient for MVP — no native mobile
  applications.
- The system uses UTC for all internal timestamps, with user-localized
  display in the UI based on browser/user preference.
