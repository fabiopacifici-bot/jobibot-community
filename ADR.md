# ADR.md — Architecture Decision Records & Codebase Analysis

## Project Overview

JobiBot Community Edition is an AI-powered job coaching platform built with Laravel 13, Livewire 4, and NativePHP 2.2. It helps candidates with CV review, job matching, and AI-driven interview simulations using a provider-agnostic AI engine.

## Architecture Decisions

### ADR-001: Provider-Agnostic LAI Engine
**Date:** 2026-06-11
**Status:** Accepted

AI interactions go through `LaiProviderInterface` with three implementations: OpenAI, Ollama, PrivateAI (vLLM-compatible).

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
│       └── PrivateAIProvider.php          # vLLM-compatible integration
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
- Provider-specific: `OPENAI_API_KEY`, `OLLAMA_BASE_URL`, `PRIVATEAI_BASE_URL`

## Testing

- PEST with `RefreshDatabase` trait (uses SQLite in-memory for speed)
- Tests cover: page loads, model relationships, provider instantiation, LAI config methods, edge cases
- Factories: `UserFactory`, `CandidateFactory`

## Build Pipeline

- **Desktop:** NativePHP 2.2 builds `.AppImage` (Linux), `.dmg` (macOS), `.exe` (Windows)
- **Mobile:** NativePHP mobile pipeline produces `.apk` (Android)
- **CI/CD:** GitHub Actions — tests on PR, builds + release on tag push (`v*`)