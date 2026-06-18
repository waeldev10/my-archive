# Project Principles

## 1. Simplicity First

Prefer simple solutions over complex ones.

Avoid unnecessary abstractions, premature optimization, and over-engineering.

---

## 2. CRUD First

Every feature must be fully usable through traditional CRUD interfaces.

The application must remain functional even when AI services are unavailable.

---

## 3. AI Enhancement, Not AI Dependency

Artificial Intelligence is an enhancement layer.

AI should improve productivity, automation, classification, and search without becoming a hard dependency.

---

## 4. API First

Every business capability should be accessible through APIs.

Web interfaces should consume the same application services used by API endpoints.

Future clients such as mobile applications, browser extensions, desktop applications, and Telegram integrations must be supported without major architectural changes.

---

## 5. Modular Architecture

The system must be organized into clear modules with well-defined responsibilities.

Modules should be independent and maintainable.

---

## 6. Separation of Concerns

Business logic must never live inside controllers.

Controllers should delegate work to application services, actions, and domain components.

---

## 7. Maintainability Over Cleverness

Readable and maintainable code is preferred over clever or highly abstract implementations.

Future maintainers should easily understand the system.

---

## 8. Consistency

Follow consistent naming conventions, coding standards, project structure, API responses, and database design across all modules.

---

## 9. Security by Default

Security is a core requirement.

The system should include:

* Authentication
* Authorization
* Rate Limiting
* Validation
* Secure File Handling
* Secure Defaults

---

## 10. Self-Hosted Friendly

The platform should be easy to install and run on personal servers and VPS environments.

Avoid unnecessary infrastructure requirements.

---

## 11. Extensibility

New archive types, integrations, AI providers, and clients should be added without major refactoring.

---

## 12. Provider Independence

The system must not be coupled to a single AI provider.

AI integrations must be implemented through abstraction layers and contracts.

---

## 13. Performance Matters

Performance should be considered from the beginning.

Prefer efficient queries, indexing strategies, caching, and asynchronous processing where appropriate.

---

## 14. Testability

Core business logic should be testable.

Critical functionality must be covered by automated tests.

---

## 15. User Data Ownership

Users own their data.

The system should make exporting, backing up, and restoring data possible in future versions.
