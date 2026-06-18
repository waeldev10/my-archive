# API Contract: Dashboard

**Base URL**: `/api/v1`
**Auth**: Sanctum token required

### GET /dashboard

Get dashboard overview data.

**Response** (200):
```json
{
  "stats": {
    "total_archives": "int",
    "archives_by_type": {
      "note": "int",
      "link": "int",
      "article": "int",
      "image": "int",
      "file": "int",
      "todo": "int",
      "plan": "int",
      "project": "int",
      "idea": "int",
      "bookmark": "int",
      "course": "int",
      "book": "int",
      "snippet": "int",
      "website": "int",
      "journal": "int",
      "prompt": "int"
    },
    "favorites_count": "int",
    "tags_count": "int"
  },
  "recent_archives": [
    {
      "id": "ulid",
      "type": "string",
      "title": "string",
      "is_favorite": "bool",
      "tags": ["string"],
      "created_at": "timestamp"
    }
  ],
  "recent_activity": [
    {
      "id": "ulid",
      "action": "string (created, updated, deleted, etc.)",
      "archive_title": "string|null",
      "archive_type": "string|null",
      "description": "string|null",
      "created_at": "timestamp"
    }
  ],
  "quick_actions": {
    "recent_types": ["string (last 3 used archive types)"]
  }
}
```

**Notes**:
- `recent_archives` returns last 10 created/updated items
- `recent_activity` returns last 10 activity log entries
- Empty state: all counts 0, empty arrays, `quick_actions.recent_types` empty
