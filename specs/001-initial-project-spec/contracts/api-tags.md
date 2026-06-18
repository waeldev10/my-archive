# API Contract: Tags

**Base URL**: `/api/v1`
**Auth**: Sanctum token required (all endpoints)

### GET /tags

List all tags for current user.

**Response** (200):
```json
{
  "data": [
    {
      "id": "ulid",
      "name": "string",
      "color": "string|null",
      "archives_count": "int"
    }
  ]
}
```

---

### POST /tags

Create a new tag.

**Request**:
```json
{
  "name": "string (required, max:100, unique per user)",
  "color": "string|null (hex color, e.g., #3B82F6)"
}
```

**Response** (201): `{ "id": "ulid", "name": "string", "color": "string|null" }`

---

### PUT /tags/{id}

Update a tag.

**Request**: `{ "name": "string", "color": "string|null" }` (all optional)\
**Response** (200): Updated tag

---

### DELETE /tags/{id}

Delete a tag. Removed from all associated archives.

**Response** (200): `{ "message": "Tag deleted." }`

---

### GET /tags/{id}/archives

Get all archives tagged with this tag (across all types).

**Query Parameters**: `page`, `per_page`, `type` (filter by archive type)\
**Response** (200): Paginated list of archive objects (same shape as archive list items)
