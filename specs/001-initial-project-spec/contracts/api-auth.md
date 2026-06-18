# API Contract: Authentication

**Base URL**: `/api/v1`
**Auth**: None (register/login), Sanctum (logout, user)

## Endpoints

### POST /auth/register

Register a new user with email and password.

**Request**:
```json
{
  "name": "string (required, max:255)",
  "email": "string (required, email, unique)",
  "password": "string (required, min:8, max:255)",
  "password_confirmation": "string (required, matches password)"
}
```

**Response** (201):
```json
{
  "message": "Account created. Please verify your email.",
  "user": { "id": "ulid", "name": "string", "email": "string" }
}
```

**Errors**: 422 (validation), 429 (rate limit)

---

### POST /auth/login

Authenticate with email and password.

**Request**:
```json
{
  "email": "string (required, email)",
  "password": "string (required)"
}
```

**Response** (200):
```json
{
  "token": "string (Sanctum token)",
  "user": { "id": "ulid", "name": "string", "email": "string", "role": "string" }
}
```

**Errors**: 401 (invalid credentials), 423 (temporarily locked out), 429 (rate limit)

---

### POST /auth/google

Authenticate or register with Google OAuth.

**Request**:
```json
{
  "google_token": "string (required, OAuth credential)"
}
```

**Response** (200):
```json
{
  "token": "string (Sanctum token)",
  "user": { "id": "ulid", "name": "string", "email": "string", "role": "string" },
  "is_new": "boolean"
}
```

---

### POST /auth/logout

Revoke current Sanctum token.

**Auth**: Sanctum token required\
**Response** (200): `{ "message": "Logged out." }`

---

### POST /auth/password/forgot

Send password reset link.

**Request**: `{ "email": "string (required, email)" }`\
**Response** (200): `{ "message": "Reset link sent." }`\
**Note**: Always returns 200 to prevent email enumeration (even if email doesn't exist).

---

### POST /auth/password/reset

Reset password using token from email.

**Request**:
```json
{
  "email": "string (required, email)",
  "token": "string (required)",
  "password": "string (required, min:8, confirmed)"
}
```

**Response** (200): `{ "message": "Password reset successfully." }`

---

### GET /auth/user

Get current authenticated user.

**Auth**: Sanctum token required\
**Response** (200):
```json
{
  "id": "ulid",
  "name": "string",
  "email": "string",
  "role": "string",
  "email_verified_at": "timestamp|null",
  "created_at": "timestamp"
}
```

---

### POST /auth/email/verify/resend

Resend verification email.

**Auth**: Sanctum token required\
**Response** (200): `{ "message": "Verification email sent." }`
