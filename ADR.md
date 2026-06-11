# ADR.md — Architecture Decision Records & Codebase Analysis

## Project Overview

JobiBot Community Edition is an AI-powered job coaching platform built with Laravel 13, Livewire 4, and NativePHP 2.2. It helps candidates with CV review, job matching, and AI-driven interview simulations using a provider-agnostic AI engine.

## Architecture Decisions

### ADR-001: Provider-Agnostic LAI Engine
**Date:** 2026-06-11
**Status:** Accepted

AI interactions go through `LaiProviderInterface` with four implementations: OpenAI, Ollama, PrivateAI (vLLM-compatible), and OpenRouter (multi-model cloud).

- Service container binding in `JobiBotServiceProvider` resolves the configured provider at runtime.
- All LAI methods are static facades on the `Lai` class — callers never interact with providers directly.
- Each provider implements `chat()`, `complete()`, and `health()` methods.
- Configuration lives in `config/jobibot.php` with `.env` overrides.

**Rationale:** Decouples the application from any single AI vendor. Users can switch between cloud (OpenAI), local (Ollama), or self-hosted (PrivateAI/vLLM) without code changes.

### ADR-002: Livewire 4 Full-Page Components
**Date:** 2026-06-11
**Status:** Accepted

Each major feature is a standalone Livewire component rendered as a full page.

- Components: `Dashboard`, `CvUpload`, `JobSearch`, `JobInterview`, `Settings`
- Each component has its own Blade view under `resources/views/livewire/`
- Navigation uses `wire:navigate` for SPA-like transitions without full page reloads.
- State is per-component; there is no shared global state store.

**Rationale:** Livewire's full-page component pattern keeps each feature self-contained, making the codebase easy to navigate and test independently.

### ADR-003: NativePHP Desktop + Mobile
**Date:** 2026-06-11
**Status:** Accepted

The same Laravel application runs as a web app, desktop app, and mobile app via NativePHP 2.2.

- `NativeAppServiceProvider` boots the desktop window via `Window::open()`.
- Mobile APK builds are produced through NativePHP's mobile pipeline.
- UI uses Tailwind CSS with responsive design; platform-specific adaptations are handled via CSS media queries and conditional layouts.
- Build artifacts are produced by GitHub Actions CI/CD.

**Rationale:** Single codebase, three platforms. NativePHP wraps the web app in native shells, avoiding the cost of maintaining separate React Native / Electron codebases.

### ADR-004: Database Schema
**Date:** 2026-06-11
**Status:** Accepted

Five core tables with standard Laravel conventions:

| Table | Purpose | Key Relationships |
|-------|---------|-------------------|
| `users` | Standard Laravel auth (name, email, password) | — |
| `candidates` | CV storage per user (cv, cv_path, bio) | `belongsTo User`, `hasMany Simulation` |
| `job_advertisements` | Job listings (title, type, salary, work_from, description, requirements, source) | `hasMany Simulation` |
| `simulations` | Interview sessions (UUID, status, scores, considerations) | `belongsTo Candidate`, `belongsTo JobAdvertisement`, `hasMany SimulationMessage` |
| `simulation_messages` | Individual interview messages (role, content) | `belongsTo Simulation` |

**Rationale:** Clean separation of concerns. A candidate can run multiple simulations against different jobs. Each simulation is an independent conversation with its own messages and scoring.

### ADR-005: Commands as Artisan CLI
**Date:** 2026-06-11
**Status:** Accepted

The `jobibot:install` command handles first-run setup (migrations, defaults).

**Rationale:** Standard Laravel pattern. Keeps setup automated and repeatable — no manual SQL or config tweaks needed.

## Directory Structure

```
app/
├── Console/Commands/InstallCommand.php    # jobibot:install Artisan command
├── JobiBot/
│   ├── Lai.php                            # Static AI engine facade
│   ├── LaiProviderInterface.php           # AI provider contract
│   ├── Exceptions/LaiException.php        # AI-related exceptions
│   └── Providers/
│       ├── OpenAIProvider.php             # OpenAI API integration
│       ├── OllamaProvider.php             # Local Ollama integration
│       ├── PrivateAIProvider.php          # vLLM-compatible integration
│       └── OpenRouterProvider.php         # OpenRouter multi-model cloud
├── Livewire/
│   ├── Dashboard.php                      # Stats + recent simulations
│   ├── CvUpload.php                       # CV upload + AI review
│   ├── JobSearch.php                      # Job search + matching
│   ├── JobInterview.php                   # AI interview simulation
│   └── Settings.php                       # AI provider configuration
├── Models/
│   ├── User.php                           # Standard Laravel user
│   ├── Candidate.php                      # belongsTo User, hasMany Simulation
│   ├── JobAdvertisement.php               # hasMany Simulation
│   ├── Simulation.php                     # UUID, scoring, status tracking
│   └── SimulationMessage.php              # Individual interview messages
└── Providers/
    ├── AppServiceProvider.php
    ├── JobiBotServiceProvider.php         # Registers LAI engine + config
    └── NativeAppServiceProvider.php       # NativePHP desktop boot
```

## AI Engine (LAI) Methods

| Method | Purpose | Returns |
|--------|---------|---------|
| `summarize(text)` | Summarize candidate CV | `{candidate_cv_summary: string}` |
| `match(cvText, jobText)` | Match CV to job description | `{job_match_percent, candidate_cv_summary}` |
| `scoreSimulation(job, conversation)` | Score completed interview | `{status, simulation_score, considerations}` |
| `review(text)` | Professional CV review + rewrite | `string` (markdown) |
| `translate(language, text)` | Translate text to language | `string` |
| `moderate(text)` | Content moderation (OpenAI only) | `bool` |
| `submitAnswer(payload)` | Interview conversation turn | `{usage, reply_message}` |
| `generateJobAd(company, bio, info)` | Generate job advertisement | `{title, type, salary, work_from, description, requirements}` |

## Routes

| Method | URI | View | Purpose |
|--------|-----|------|---------|
| GET | / | dashboard | Dashboard with stats |
| GET | /cv | cv | CV upload + review |
| GET | /jobs | jobs | Job search + matching |
| GET | /interview | interview | AI interview simulation |
| GET | /settings | settings | AI provider config |

## Configuration

- `config/jobibot.php` — AI provider selection, model, temperature, storage limits
- `.env` keys: `JOBIBOT_AI_PROVIDER`, `JOBIBOT_AI_MODEL`, `JOBIBOT_AI_TEMPERATURE`
- Provider-specific: `OPENAI_API_KEY`, `OLLAMA_BASE_URL`, `PRIVATEAI_BASE_URL`, `OPENROUTER_API_KEY`

## Testing

- PEST with `RefreshDatabase` trait (uses SQLite in-memory for speed)
- Tests cover: page loads, model relationships, provider instantiation, LAI config methods, edge cases
- Factories: `UserFactory`, `CandidateFactory`

## Build Pipeline

- **Desktop:** NativePHP 2.2 builds `.AppImage` (Linux), `.dmg` (macOS), `.exe` (Windows)
- **Mobile:** NativePHP mobile pipeline produces `.apk` (Android)
- **CI/CD:** GitHub Actions — tests on PR, builds + release on tag push (`v*`)

### ADR-006: Cross-Device Sync via AI Hub
**Date:** 2026-06-12
**Status:** Proposed

Desktop and mobile apps each maintain their own local SQLite databases (ADR-003).
Users who run both need their CVs, job searches, and interview simulations to stay in
sync without a cloud backend.

**Solution:** Integrate the existing AI Hub protocol (`/mnt/d/ai-hub`) as a local
discovery + sync bridge. The desktop app embeds a lightweight hub server; the mobile
app discovers it on the local network.

**Architecture:**

```
┌─────────────────────┐        ┌─────────────────────┐
│  Desktop JobiBot    │        │  Mobile JobiBot     │
│  (Electron/Native)  │        │  (Android APK)      │
│                     │        │                     │
│  ┌───────────────┐  │        │                     │
│  │ Embedded Hub  │◄─┼────────┤  AiHubClient        │
│  │ (port 8764)   │  │  LAN   │  (discover + sync)  │
│  └───────┬───────┘  │        └─────────────────────┘
│          │          │
│  ┌───────▼───────┐  │
│  │ SyncProvider  │  │
│  │ - sync-cv     │  │
│  │ - sync-jobs   │  │
│  │ - sync-sims   │  │
│  └───────────────┘  │
│                     │
│  SQLite DB          │
└─────────────────────┘
```

**Components:**

| Layer | What | Where |
|-------|------|-------|
| **Embedded Hub** | Mini Laravel server inside desktop app, registers capabilities on startup | `app/AiHub/EmbeddedHubServer.php` |
| **SyncProvider** | Implements `AiHubProviderInterface` — exposes CV, jobs, simulations as sync capabilities | `app/AiHub/SyncProvider.php` |
| **AiHubClient** | Mobile-side client — discovers desktop hub, calls capabilities, applies deltas | `app/AiHub/AiHubClient.php` |
| **SyncService** | Conflict resolution, delta tracking, last-sync timestamps | `app/AiHub/SyncService.php` |

**API (reuses existing AI Hub protocol):**

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `GET /ai-hub/capabilities` | GET | Mobile discovers desktop capabilities |
| `POST /ai-hub/requests` | POST | Mobile requests sync-pull or sync-push |
| `GET /ai-hub/requests?endpoint_id=X` | GET | Desktop polls for pending sync requests |

**Sync Capabilities:**

| Capability ID | Parameters | Response |
|---------------|------------|----------|
| `sync-cv` | `{action: "pull|push", since: "ISO", data?: {...}}` | `{candidates: [...], lastSync: "ISO"}` |
| `sync-jobs` | `{action: "pull|push", since: "ISO", data?: {...}}` | `{jobs: [...], lastSync: "ISO"}` |
| `sync-sims` | `{action: "pull|push", since: "ISO", data?: {...}}` | `{simulations: [...], lastSync: "ISO"}` |

**Discovery flow:**

1. Desktop starts → Embedded Hub binds to `0.0.0.0:8764`
2. Desktop registers as endpoint `{name: "JobiBot Desktop", type: "desktop", capabilities: [sync-cv, sync-jobs, sync-sims]}`
3. Mobile scans local network for hub (mDNS or configurable IP)
4. Mobile calls `GET /ai-hub/capabilities` → finds desktop's sync capabilities
5. Mobile creates sync requests → desktop processes against its SQLite
6. Conflict resolution: last-write-wins with timestamp comparison

**Dependencies:**

- Existing: `ai-hub-laravel-provider` package (from `/mnt/d/ai-hub/packages/`)
- New: Embedded hub bootstrap in desktop `NativeAppServiceProvider`
- New: `ai_hub_enabled` config flag + `.env` keys for hub port, token

**Phases:**

| Phase | Deliverable | Est. |
|-------|------------|------|
| 1 | Install `ai-hub-laravel-provider`, wire `SyncProvider`, boot embedded hub on desktop | 1-2 sessions |
| 2 | Implement `AiHubClient` on mobile side, basic pull sync | 1-2 sessions |
| 3 | Two-way sync with conflict resolution, UI toggle | 1 session |
| 4 | Auto-discovery (mDNS), background sync, tests | 1-2 sessions |

**Rationale:** Reuses an existing, tested protocol instead of building a custom sync
layer from scratch. The AI Hub already handles registration, capability discovery,
request routing, and authentication. JobiBot only needs to implement the provider
interface and add a thin client.