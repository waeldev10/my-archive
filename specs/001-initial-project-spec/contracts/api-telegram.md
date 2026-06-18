# API Contract: Telegram

**Base URL**: `/api/v1`
**Auth**: Sanctum token required (user endpoints)

### POST /telegram/connect

Generate connection code/link for Telegram bot.

**Response** (200):
```json
{
  "bot_username": "string",
  "connection_code": "string (temporary code for /start command)",
  "expires_at": "timestamp"
}
```

**Notes**: User visits Telegram, sends `/start <code>` to the bot to link
their account.

---

### GET /telegram/status

Get Telegram connection status.

**Response** (200):
```json
{
  "connected": "bool",
  "telegram_username": "string|null",
  "connected_at": "timestamp|null"
}
```

---

### DELETE /telegram/disconnect

Disconnect Telegram account.

**Response** (200): `{ "message": "Telegram disconnected." }`

---

## Telegram Bot API

The Telegram bot communicates internally with the application via Sanctum token
(not a public API). The bot receives webhook events from Telegram and processes
them through internal application services.

### Inbound Message Handling

| Message Type | Archive Type Created | Notes |
|-------------|---------------------|-------|
| Text message (no URL) | Note | Full text becomes description |
| Text message (contains URL) | Link | URL extracted, preview fetched async |
| Photo | Image | Photo saved to filesystem, caption as description |
| Document/File | File | File saved to filesystem |
| Video | File | Treated as file attachment |
| Audio | File | Treated as file attachment |

### Bot Commands

| Command | Action |
|---------|--------|
| `/start <code>` | Connect account with connection code |
| `/start` | Show help if not connected |
| `/help` | Show available commands |
| `/recent` | Show last 5 archives |
| `/search <query>` | Search archives, return top 5 results |
| `/summarize <id>` | AI-summarize archive by ID (requires AI configured) |
| `/tags <id>` | Suggest tags for archive by ID (requires AI configured) |

### Bot Response Format

All bot responses are plain text with Markdown formatting for readability.
Search results include archive type emoji prefix and short title + ID.
