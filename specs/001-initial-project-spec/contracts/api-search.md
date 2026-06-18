# API Contract: Search

**Base URL**: `/api/v1`
**Auth**: Sanctum token required

### GET /search

Full-text search across all user archives.

**Query Parameters**:
- `q` (string, required, min:2) — search query
- `type` (string, optional) — filter by archive type
- `tag` (string, optional) — filter by tag name
- `favorite` (bool, optional) — filter by favorite status
- `page` (int, default: 1)
- `per_page` (int, default: 20, max: 50)

**Response** (200):
```json
{
  "query": "string",
  "total_results": "int",
  "groups": [
    {
      "type": "string",
      "count": "int",
      "results": [
        {
          "id": "ulid",
          "type": "string",
          "title": "string",
          "snippet": "string (highlighted excerpt)",
          "is_favorite": "bool",
          "tags": [{"id": "ulid", "name": "string", "color": "string|null"}],
          "created_at": "timestamp",
          "relevance_score": "float"
        }
      ]
    }
  ],
  "meta": {
    "current_page": "int",
    "last_page": "int",
    "total": "int"
  }
}
```

**Notes**:
- Results ranked by relevance (PG ts_rank with weighted title > description > tags)
- Snippets show matching context with surrounding text
- Empty results return `{ "total_results": 0, "groups": [] }` with helpful message
