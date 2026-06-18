# API Contract: Admin

**Base URL**: `/api/v1`
**Auth**: Sanctum token required + Admin role (all endpoints)

### GET /admin/users

List all users.

**Query Parameters**: `page`, `per_page`, `role` (filter: admin, user)\
**Response** (200):
```json
{
  "data": [
    {
      "id": "ulid",
      "name": "string",
      "email": "string",
      "role": "string",
      "email_verified_at": "timestamp|null",
      "archives_count": "int",
      "created_at": "timestamp"
    }
  ],
  "meta": { "current_page": "int", "last_page": "int", "total": "int" }
}
```

---

### PUT /admin/users/{id}/role

Change a user's role.

**Request**: `{ "role": "admin|user (required)" }`\
**Response** (200): Updated user object

---

### GET /admin/settings

Get system-wide settings.

**Response** (200):
```json
{
  "registration_open": "bool",
  "default_ai_provider": "string|null",
  "app_name": "string",
  "app_url": "string"
}
```

---

### PUT /admin/settings

Update system-wide settings.

**Request**:
```json
{
  "registration_open": "bool (optional)",
  "default_ai_provider": "string|null (optional)"
}
```
**Response** (200): Updated settings
