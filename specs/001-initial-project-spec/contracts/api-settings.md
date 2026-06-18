# API Contract: Settings

**Base URL**: `/api/v1`
**Auth**: Sanctum token required (all endpoints)

### GET /settings

Get user preferences.

**Response** (200):
```json
{
  "user": {
    "id": "ulid",
    "name": "string",
    "email": "string",
    "email_verified_at": "timestamp|null",
    "avatar": "string|null",
    "role": "string",
    "created_at": "timestamp"
  },
  "preferences": {
    "theme": "light|dark|system",
    "locale": "string",
    "ai_provider": "string|null",
    "ai_model": "string|null",
    "notifications_enabled": "bool"
  },
  "tokens": [
    {
      "id": "ulid",
      "name": "string",
      "last_used_at": "timestamp|null",
      "created_at": "timestamp"
    }
  ]
}
```

---

### PUT /settings/profile

Update user profile.

**Request**: `{ "name": "string (optional)", "avatar": "file (optional)" }`\
**Response** (200): Updated user object

---

### PUT /settings/preferences

Update user preferences.

**Request**:
```json
{
  "theme": "light|dark|system (optional)",
  "locale": "string (optional)",
  "ai_provider": "string|null (optional)",
  "ai_api_key": "string|null (optional)",
  "ai_model": "string|null (optional)",
  "notifications_enabled": "bool (optional)"
}
```
**Response** (200): Updated preferences object

---

### POST /settings/tokens

Create a new API token.

**Request**: `{ "name": "string (required, max:255)" }`\
**Response** (201):
```json
{
  "id": "ulid",
  "name": "string",
  "token": "string (plain text — shown once)",
  "created_at": "timestamp"
}
```
**Note**: Token plain text is only returned at creation time. Store it
immediately.

---

### DELETE /settings/tokens/{id}

Revoke an API token.

**Response** (200): `{ "message": "Token revoked." }`
