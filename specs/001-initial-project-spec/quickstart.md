# Quickstart Validation Guide: Initial Platform — My Archive

**Phase**: 1 (Design)
**Date**: 2026-06-16
**Prerequisites**: [data-model.md](data-model.md), [contracts/](contracts/)

## Prerequisites

- PHP 8.3+ with Composer
- PostgreSQL 16+
- Redis 7+
- Node.js 20+ with npm
- Telegram bot token (for Telegram integration testing)

## Setup Commands

```bash
# Clone and install
composer install
npm install

# Environment
cp .env.example .env
# Edit .env: DB_CONNECTION=pgsql, Redis config, mail config

# Generate key + migrate
php artisan key:generate
php artisan migrate

# Build assets
npm run build

# Start development servers
composer run dev
```

## Validation Scenarios

### Scenario 1: Authentication Flow

**Endpoint/Page**: `/register`, `/login`, `/dashboard`

**Steps**:
1. Visit `/register` and create account with `test@example.com` / `password`
2. Check email inbox for verification link (or use `php artisan tinker` to
   manually verify)
3. Log in at `/login` with credentials
4. Verify redirect to `/dashboard`

**Expected**: User registered, email verification sent, dashboard loads with
empty state and "Create your first archive" prompt.

---

### Scenario 2: Archive CRUD (Note type)

**Endpoint/Page**: `/archives/note`

**Steps**:
1. Navigate to `/archives/note` — verify empty state
2. Click "Create" — fill title "Test Note" and description "Hello world"
3. Submit — verify redirect to detail view with success message
4. Click "Edit" — change title to "Updated Note" — save
5. Navigate to list — verify "Updated Note" appears
6. Open detail — click "Delete" — confirm — verify removed from list

**Expected**: Full create → read → update → delete cycle works for Note type.

---

### Scenario 3: Tag Assignment & Filtering

**Steps**:
1. Create a Note titled "Laravel Tips" and assign tags: `laravel`, `php`
2. Create a Link titled "Laravel Docs" and assign tag: `laravel`
3. Navigate to `/tags` — verify both tags exist
4. Click on `laravel` tag — verify both archives appear (cross-type)
5. Click on `php` tag — verify only the Note appears

**Expected**: Tags are global — same tag works across different archive types.

---

### Scenario 4: Global Search

**Steps**:
1. Create several archives across different types with distinct content
2. Use search bar with keyword that matches one archive — verify result shown
3. Search with term that matches multiple types — verify grouped by type
4. Filter by archive type — verify results limited
5. Search with non-matching term — verify empty state with helpful message

**Expected**: Full-text search returns weighted, grouped, filterable results.

---

### Scenario 5: Dashboard Interaction

**Steps**:
1. Log in with no archives — verify empty state with quick-action buttons
2. Create archives of 3 different types
3. Return to dashboard — verify recent archives, type breakdown, activity feed
4. Click "New Note" quick-action — verify redirect to create form

**Expected**: Dashboard shows real-time stats and recent activity.

---

### Scenario 6: Settings & Dark Mode

**Steps**:
1. Navigate to `/settings`
2. Toggle theme to dark — verify entire UI switches
3. Update profile name — verify change persists
4. Create API token — verify token shown once, then hidden
5. Log out and back in — verify dark mode persists

**Expected**: Theme persists across sessions; API tokens manageable from UI.

---

### Scenario 7: REST API (via curl or similar)

**Steps**:
```bash
# Register
curl -X POST /api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"API User","email":"api@test.com","password":"password","password_confirmation":"password"}'

# Login
TOKEN=$(curl -s -X POST /api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"api@test.com","password":"password"}' | jq -r '.token')

# Create archive via API
curl -X POST /api/v1/archives/note \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"title":"API Note","description":"Created via API"}'

# List archives
curl -H "Authorization: Bearer $TOKEN" /api/v1/archives/note

# Search
curl -H "Authorization: Bearer $TOKEN" "/api/v1/search?q=API"
```

**Expected**: Full CRUD and search accessible via API with Sanctum tokens.

---

### Scenario 8: Admin User Management

**Steps**:
1. Register two accounts: `admin@test.com`, `user@test.com`
2. Promote `admin@test.com` to admin role via `php artisan tinker` or seeder
3. Log in as admin — navigate to `/admin/users` — verify both users listed
4. Change `user@test.com` to admin — verify role updated
5. Log in as regular user — verify `/admin/users` returns 403

**Expected**: Admin sees all users, can manage roles; regular users blocked.

---

### Scenario 9: AI Features (if AI configured)

**Steps**:
1. Configure AI provider in settings (API key)
2. Create a Note with substantial content
3. Request AI tag suggestions — verify suggestions appear
4. View the Note — request summary — verify summary generated
5. Open AI chat (global mode) — ask "What have I saved about Laravel?"
6. Open AI chat from a specific archive — verify chat is scoped to that item
7. Disable AI provider — verify all AI features show "AI not configured"
   message and CRUD/search still works

**Expected**: AI features work when configured; platform functional without AI.

---

### Scenario 10: Telegram Integration

**Steps**:
1. Navigate to `/settings` → Telegram → generate connection code
2. Open Telegram, send `/start <code>` to bot
3. Send a text message to bot — verify Note appears in web interface
4. Forward a URL to bot — verify Link appears with URL
5. Send `/recent` — verify bot responds with recent archives
6. Disconnect Telegram from settings — verify bot no longer accepts messages

**Expected**: Telegram messages become archives; bot commands work.

## File & Archive Type Coverage

Verify all 16 archive types independently:

- [ ] Note — create with title + description
- [ ] Link — create with URL (verify domain extracted)
- [ ] Article — create with rich text description
- [ ] Image — upload image (verify stored privately)
- [ ] File — upload document (verify access control)
- [ ] Todo — set due date + priority, mark complete
- [ ] Plan — set date range + status + progress
- [ ] Project — set status + optional repo URL
- [ ] Idea — basic note-like entry
- [ ] Bookmark — basic entry with optional URL
- [ ] Course — set provider + completion status
- [ ] Book — add author, ISBN, reading status
- [ ] Snippet — add code language + code content
- [ ] Website — add URL + optional feed URL
- [ ] Journal — set date + mood + location
- [ ] Prompt — basic entry for saved prompts

## Running Tests

```bash
# Run all tests
composer run test

# Run specific test suites
php artisan test --filter=AuthTest
php artisan test --filter=ArchiveTest
php artisan test --filter=TagTest
php artisan test --filter=SearchTest
php artisan test --filter=ApiTest
```
