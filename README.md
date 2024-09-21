# FlagForge

FlagForge is a small LaunchDarkly-style feature flag platform built with Laravel + Vue.
It supports project-based flags, targeting rules, percentage rollout, SDK evaluation, audit logs, basic experiment tracking, and realtime updates over Reverb.

## Stack

- PHP 8.2+ / Laravel 11
- Vue 3 + Inertia + Tailwind
- PostgreSQL + Redis
- Laravel Reverb for realtime broadcast
- Docker Compose (Laravel Sail)

## Current Features

- Feature flag CRUD under projects
- Rule evaluation engine (`country`, attribute match, list matching)
- Percentage rollout (deterministic bucketing)
- SDK API endpoint: `POST /api/v1/evaluate`
- SDK token auth middleware with hashed keys
- Redis-backed cached evaluations
- Audit trail entries on project/flag updates
- Experiment event tracking endpoint
- Realtime flag update broadcast (`projects.{project_key}.flags`)

## Local Setup

1. Install dependencies:
   - `composer install`
   - `npm install`
2. Copy env:
   - `cp .env.example .env`
3. Start services:
   - `./vendor/bin/sail up -d`
4. Migrate and seed:
   - `./vendor/bin/sail artisan migrate --seed`
5. Start frontend dev server:
   - `npm run dev`
6. Start Reverb (in a separate terminal) for realtime flag updates:
   - `./vendor/bin/sail artisan reverb:start`

Open `http://localhost`.

## Demo SDK token

Seeder creates one local SDK key hash for token:

`ff_demo_local_key`

Use it as:

`Authorization: Bearer ff_demo_local_key`

## API quick example

`POST /api/v1/evaluate`

```json
{
  "context": {
    "user_identifier": "user-123",
    "country": "PH",
    "segments": ["beta"]
  }
}
```

## Tests

- `php artisan test`
- `npm run build`
