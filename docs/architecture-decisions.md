# Architecture Decisions

## Application Type

Monolithic Laravel Application

The system contains:

* Web Interface
* REST API

within the same Laravel project.

---

## Backend

* Laravel 13

---

## Frontend

* Livewire v4
* Alpine.js
* Tailwind CSS v4

---

## Database

* PostgreSQL

---

## Cache & Queue

* Redis

---

## Testing

* Pest

---

## Authentication

* Email Authentication
* Google OAuth
* Email Verification
* Password Reset
* Rate Limiting

---

## User Roles

Only:

* Admin
* User

No advanced permission system.

---

## Identifiers

All entities use ULID.

---

## Architecture Style

* Modular Architecture
* Service Layer
* Repository Pattern
* DTOs
* Actions
* Events & Listeners

---

## AI Architecture

AI Provider Abstraction Layer

Supported providers may include:

* DeepSeek
* OpenAI
* Gemini

The application must not depend on a single AI provider.

---

## API Strategy

API First Design

Every business capability should be accessible through:

* Web Interface
* API Endpoint

---

## Telegram

Telegram integration is part of V1.
