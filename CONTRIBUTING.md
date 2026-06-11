# Contributing to JobiBot Community Edition

Thanks for your interest in contributing! Here's how to get started.

## Code of Conduct

Be respectful. We're building tools to help people advance their careers — keep that mission in mind.

## How to Report a Bug

1. Check [existing issues](https://github.com/fabiopacifici-bot/jobibot-community/issues) for duplicates.
2. Open a [bug report](https://github.com/fabiopacifici-bot/jobibot-community/issues/new?template=bug_report.md) with:
   - Steps to reproduce
   - Expected vs actual behavior
   - Environment: PHP version, OS, AI provider, database

## How to Suggest a Feature

1. Check the [existing issues](https://github.com/fabiopacifici-bot/jobibot-community/issues) and [project roadmap](https://github.com/orgs/fabiopacifici-bot/projects).
2. Open a [feature request](https://github.com/fabiopacifici-bot/jobibot-community/issues/new?template=feature_request.md) describing the use case and desired outcome.

## Development Setup

```bash
git clone https://github.com/fabiopacifici-bot/jobibot-community.git
cd jobibot-community
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan jobibot:install
```

## Coding Standards

- **PHP:** PSR-12 + Laravel conventions, enforced by [Laravel Pint](https://laravel.com/docs/pint)
- **Tests:** PEST with `RefreshDatabase` — tests must pass before merging
- **Commit messages:** Conventional commits (`feat:`, `fix:`, `test:`, `docs:`, `style:`, `refactor:`)

Run before submitting:

```bash
./vendor/bin/pint                          # Auto-fix code style
php artisan test --parallel                # All tests must pass
```

## Pull Request Process

1. Create a feature branch from `main`: `feat/your-feature-name`
2. Make your changes with passing tests
3. Run `./vendor/bin/pint` to fix code style
4. Submit a PR against `main`
5. CI must pass (tests + lint)
6. A maintainer will review and merge

## Branch Naming

- `feat/*` — new features
- `fix/*` — bug fixes
- `docs/*` — documentation
- `test/*` — test additions/improvements
- `refactor/*` — code restructuring
- `style/*` — code style fixes

## Questions?

Open a [discussion](https://github.com/fabiopacifici-bot/jobibot-community/discussions) or reach out to the maintainer.