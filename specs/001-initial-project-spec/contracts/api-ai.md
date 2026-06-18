# API Contract: AI

**Base URL**: `/api/v1`
**Auth**: Sanctum token required (all endpoints)

### POST /ai/classify

Suggest an archive type for given content.

**Request**: `{ "content": "text (required)" }`\
**Response** (200):
```json
{
  "suggested_type": "string",
  "alternatives": ["string"],
  "confidence": "float"
}
```

---

### POST /ai/tags

Suggest tags for given content.

**Request**: `{ "content": "text (required", "archive_id": "ulid|null (optional)" }`\
**Response** (200):
```json
{
  "suggestions": [
    { "name": "string", "confidence": "float" }
  ]
}
```

---

### POST /ai/summarize

Generate a summary of archive content.

**Request**: `{ "archive_id": "ulid (required)" }`\
**Response** (200):
```json
{
  "summary": "text",
  "key_points": ["string"]
}
```

---

### POST /ai/chat

Send a message in an AI conversation.

**Request**:
```json
{
  "message": "string (required)",
  "conversation_id": "ulid|null (null = new conversation)",
  "archive_id": "ulid|null (null = global mode, set = archive context)"
}
```

**Response** (200):
```json
{
  "conversation_id": "ulid",
  "reply": "text",
  "sources": [
    { "archive_id": "ulid", "title": "string", "type": "string", "relevance": "float" }
  ]
}
```

**Notes**:
- `archive_id` in the request scopes the chat to one archive (archive context mode)
- Omit `archive_id` for global knowledge retrieval mode
- `sources` array is populated in global mode to indicate which archives informed the answer

---

### GET /ai/conversations

List user's AI conversations.

**Query Parameters**: `page`, `per_page`\
**Response** (200): Paginated list of conversations with last message preview

---

### GET /ai/conversations/{id}

Get a conversation with all messages.

**Response** (200):
```json
{
  "id": "ulid",
  "title": "string|null",
  "archive_id": "ulid|null",
  "messages": [
    { "role": "user|assistant", "content": "text", "created_at": "timestamp" }
  ],
  "created_at": "timestamp"
}
```

---

### DELETE /ai/conversations/{id}

Delete a conversation.

**Response** (200): `{ "message": "Conversation deleted." }`
