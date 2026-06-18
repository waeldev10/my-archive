# API Contract: Archives

**Base URL**: `/api/v1`
**Auth**: Sanctum token required (all endpoints)

## Overview

All 16 archive types share a common CRUD pattern. Replace `{type}` with:
note, link, article, image, file, todo, plan, project, idea, bookmark, course,
book, snippet, website, journal, prompt.

## Common Endpoints

### GET /archives/{type}

List archives of a specific type.

**Query Parameters**:
- `page` (int, default: 1)
- `per_page` (int, default: 20, max: 100)
- `sort` (string: created_at, updated_at, title; default: created_at)
- `order` (string: asc, desc; default: desc)
- `favorite` (bool: filter by favorite status)
- `tag` (string: filter by tag name)

**Response** (200):
```json
{
  "data": [
    {
      "id": "ulid",
      "type": "string",
      "title": "string",
      "description": "string|null",
      "is_favorite": "bool",
      "tags": [{"id": "ulid", "name": "string", "color": "string|null"}],
      "type_specific": { /* type-specific fields */ },
      "created_at": "timestamp",
      "updated_at": "timestamp"
    }
  ],
  "meta": {
    "current_page": "int",
    "last_page": "int",
    "per_page": "int",
    "total": "int"
  }
}
```

---

### POST /archives/{type}

Create a new archive.

**Request** (multipart/form-data for files/images):
```json
{
  "title": "string (required, max:255)",
  "description": "string|null",
  "is_favorite": "bool (default: false)",
  "tags": ["string (tag names)"],
  // type-specific fields (see per-type contracts below)
}
```

**Response** (201): Single archive object (same shape as list item)

---

### GET /archives/{type}/{id}

Get a single archive.

**Response** (200): Single archive object with all type-specific fields\
**Errors**: 403 (not owner), 404 (not found)

---

### PUT /archives/{type}/{id}

Update an archive.

**Request**: Same shape as create, all fields optional\
**Response** (200): Updated archive object

---

### DELETE /archives/{type}/{id}

Delete an archive (soft delete).

**Response** (200): `{ "message": "Archive moved to trash." }`\
**Errors**: 403 (not owner)

---

### POST /archives/{type}/{id}/restore

Restore from trash.

**Response** (200): `{ "message": "Archive restored." }`

---

### DELETE /archives/{type}/{id}/force

Permanently delete.

**Auth**: Owner only\
**Response** (200): `{ "message": "Archive permanently deleted." }`

---

### POST /archives/{type}/{id}/favorite

Toggle favorite status.

**Response** (200):
```json
{ "id": "ulid", "is_favorite": "bool" }
```

---

## Type-Specific Fields

### Note, Article, Idea, Bookmark, Prompt
No type-specific fields — content stored in `description`.

### Link
- `url` (string, required) — the URL
- `domain` (string, optional) — extracted from URL
- `preview_image` (string, optional) — URL to preview image
- `preview_description` (string, optional) — page preview text

### Image
- `file` (file, required) — uploaded image (max 10MB)
- `alt_text` (string, optional)

### File
- `file` (file, required) — uploaded file (max 25MB)
- `original_name` (string, read-only)

### Todo
- `due_date` (date, optional)
- `completed_at` (timestamp, optional, null to unset)
- `priority` (enum: low, medium, high, default: medium)

### Plan
- `start_date` (date, optional)
- `end_date` (date, optional)
- `status` (enum: draft, active, completed, cancelled, default: draft)
- `progress` (int, 0–100, default: 0)

### Project
- `start_date` (date, optional)
- `end_date` (date, optional)
- `status` (enum: idea, planning, active, paused, completed, cancelled, default: idea)
- `repository_url` (string, optional)

### Course
- `provider` (string, optional)
- `platform` (string, optional)
- `completion_status` (enum: not_started, in_progress, completed, default: not_started)
- `progress` (int, 0–100, default: 0)

### Book
- `author` (string, optional)
- `isbn` (string, optional, max 20)
- `pages` (int, optional)
- `status` (enum: to_read, reading, finished, abandoned, default: to_read)
- `started_at` (date, optional)
- `finished_at` (date, optional)

### Snippet
- `code_language` (string, optional)
- `code_content` (text, required)
- `source_url` (string, optional)

### Website
- `url` (string, required)
- `domain` (string, optional)
- `feed_url` (string, optional)

### Journal
- `entry_date` (date, required)
- `mood` (string, optional, max 50)
- `location` (string, optional, max 255)
