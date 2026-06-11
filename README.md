# JobiBot Community Edition 🤖

**Your Open-Source AI Job Coach — CV Review, Job Matching & Interview Simulation**

[![PHP 8.3+](https://img.shields.io/badge/PHP-8.3%2B-777bb4?logo=php)](https://php.net)
[![Laravel 13](https://img.shields.io/badge/Laravel-13-red?logo=laravel)](https://laravel.com)
[![Tests](https://img.shields.io/badge/tests-43%20%E2%9C%94-brightgreen)](https://github.com/fabiopacifici-bot/jobibot-community/actions)
[![License](https://img.shields.io/badge/license-MIT-blue)](LICENSE)

JobiBot Community Edition is a Laravel-based, AI-powered desktop and mobile application designed to help candidates prepare for the job market. Upload your CV, search for matching jobs, and run realistic AI interview simulations — all powered by your own AI provider.

## ✨ Features

- **📄 CV Upload & Review** — Upload your CV and get AI-powered professional feedback and rewriting suggestions
- **🔍 Job Search & Matching** — Search remote jobs and get AI-powered CV-to-job matching scores
- **🎯 Interview Simulation** — Run realistic job interview simulations with an AI recruiter that adapts to your answers
- **⚙️ Provider-Agnostic AI** — Use OpenAI, Ollama (local), or Private AI (vLLM) — switch providers without code changes
- **📊 Smart Dashboard** — Track your simulation history, scores, and progress over time
- **🖥️ Desktop & Mobile** — Run as a web app, desktop app (Linux/macOS/Windows), or Android APK via NativePHP

## 🧱 Tech Stack

- **Backend:** Laravel 13, PHP 8.3+
- **Frontend:** Livewire 4, Tailwind CSS 4, Alpine.js
- **Desktop/Mobile:** NativePHP 2.2
- **Testing:** PEST 4, RefreshDatabase
- **AI Providers:** OpenAI, Ollama, Private AI (vLLM-compatible)
- **CI/CD:** GitHub Actions

## 📋 Requirements

- PHP 8.3+
- [Composer](https://getcomposer.org/)
- [Node.js 20+](https://nodejs.org/) & npm
- SQLite (default), MySQL 8+, or PostgreSQL 15+
- Optional: Ollama or vLLM for local AI; OpenAI API key for cloud AI

## 🚀 Quick Start

```bash
# Clone the repository
git clone https://github.com/fabiopacifici-bot/jobibot-community.git
cd jobibot-community

# Install PHP dependencies
composer install

# Install frontend dependencies & build
npm install && npm run build

# Set up environment
cp .env.example .env
php artisan key:generate

# Run the installer (migrates database)
php artisan jobibot:install

# Start the development server
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000) in your browser.

## 🔧 AI Provider Setup

JobiBot works with three AI providers. Configure your choice in `.env`:

### OpenAI (Cloud)

```env
JOBIBOT_AI_PROVIDER=openai
JOBIBOT_AI_MODEL=gpt-4o
OPENAI_API_KEY=sk-your-key-here
```

### Ollama (Local)

```env
JOBIBOT_AI_PROVIDER=ollama
JOBIBOT_AI_MODEL=gemma3
OLLAMA_BASE_URL=http://localhost:11434
```

Make sure [Ollama](https://ollama.com) is running and your model is pulled:
```bash
ollama pull gemma3
```

### Private AI / vLLM (Self-Hosted)

```env
JOBIBOT_AI_PROVIDER=privateai
JOBIBOT_AI_MODEL=qwen3-7b
PRIVATEAI_BASE_URL=http://localhost:8005
```

vLLM must expose an OpenAI-compatible API endpoint.

> 💡 You can also configure the provider through the **Settings** page in the app UI.

## 📖 Usage

### Dashboard
View your simulation stats: total sessions, completed interviews, and average scores. Quick-launch buttons guide you to each feature.

### CV Upload
Upload your CV and let the AI review it for professional standards (Europass guidelines). Get a revised version with improved formatting and structure.

### Job Search
Browse remote job listings by category. Select a job and run an AI match against your CV to see how well you fit.

### Interview Simulation
Choose a job and start a simulated interview. The AI recruiter asks 5 introductory questions followed by 5 technical questions, provides feedback on your answers, and gives a final score with areas for improvement.

## 🖥️ Desktop (NativePHP)

Build a native desktop app for your platform:

```bash
# Install NativePHP desktop dependencies
php artisan native:install

# Build for your current OS
php artisan native:build
```

Outputs: `.AppImage` (Linux), `.dmg` (macOS), `.exe` (Windows)

## 📱 Mobile (Android)

Build an Android APK:

```bash
# Build Android .apk
php artisan native:build mobile
```

Download the APK from the [latest release](https://github.com/fabiopacifici-bot/jobibot-community/releases).

## 🧪 Development

### Run tests

```bash
php artisan test --parallel
```

43 tests covering feature pages, LAI engine, models, provider instantiation, and edge cases.

### Code style

```bash
./vendor/bin/pint
```

Laravel Pint enforces PSR-12 and Laravel conventions.

### Architecture

See **[ADR.md](./ADR.md)** for a complete architectural overview, design decisions, and codebase analysis.

## 🤝 Contributing

Contributions are welcome! See **[CONTRIBUTING.md](./CONTRIBUTING.md)** for guidelines on reporting bugs, suggesting features, and submitting pull requests.

## 📄 License

MIT — Copyright (c) 2026 [Fabio Pacifici (pacificDev)](https://github.com/pacificDev)

JobiBot Community Edition is open-source software. See [LICENSE](./LICENSE) for full terms.

## 🙏 Credits

Created by [Fabio Pacifici](https://github.com/pacificDev) — part of the [NSA Agency](https://github.com/fabiopacifici-bot) ecosystem.

Built with ❤️ using Laravel, Livewire, NativePHP, and open-source AI.