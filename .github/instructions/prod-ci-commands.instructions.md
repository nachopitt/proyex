---
name: production-and-ci-command-instructions
description: "Instructions for running commands, tests, database migrations, and setup in CI/Staging/Production lanes"
applyTo: ".github/workflows/**/*.yml, DEPLOYMENT.md, docker-compose.prod.yml"
---

# CI, Staging, and Production Command Rules

This repository operates on a "Build Once, Promote" deployment architecture.
No source repositories are cloned, built, or modified on the production/staging server; all deployment artifacts live in pre-built Docker containers (`app` and `web`) pulled from GHCR.

## ⚠️ PROD & STAGING SERVER RUNTIME RULES ⚠️

1. **Never attempt to build images, run `npm build`, or run `composer install` on the remote server**:
   - The production server ONLY runs pre-built images.
   - The server has NO source files. Command-line interactions on the server must use `docker compose exec -T app <command>`.

2. **Database Migrations and Artisan commands in Production/Staging**:
   - Run commands non-interactively using `-T` via the `app` service:
     `docker compose exec -T app php artisan migrate --force`
     `docker compose exec -T app php artisan cache:clear`
     `docker compose exec -T app php artisan config:cache`
   - Do **NOT** use `workspace` in production. The `workspace` container is for local development only and is omitted from `docker-compose.prod.yml`.

3. **Running tests in CI**:
   - When configuring CI workflows or testing, run PHPUnit via:
     `docker compose exec workspace php artisan test` (if local) or inside standard runners.
   - Do **not** run raw PHPUnit/Artisan on hosts without ensuring dependencies.
