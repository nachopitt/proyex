# Docker Execution Instructions

This project runs inside a Docker-composed environment. All developers and Large Language Models (LLMs) **must never** execute commands like PHP, Composer, Artisan, npm, or PHPUnit directly on the host machine. Instead, always target the development docker containers (specifically `workspace`).

## ⚠️ CRITICAL COMMAND RULES ⚠️

1. **PHP, Artisan, and Composer**:
   - Run inside the `workspace` container using `docker compose exec workspace`.
   - **Artisan**: `docker compose exec workspace php artisan <command>`
   - **Composer**: `docker compose exec workspace composer <command>`
   - **PHPUnit / Tests**: `docker compose exec workspace php artisan test` or `docker compose exec workspace ./vendor/bin/phpunit`

2. **Npm running tool / Vite**:
   - Run inside the `workspace` container: `docker compose exec workspace npm <command>`
   - **Building assets**: `docker compose exec workspace npm run build`
   - Only if you need to run Vite dev manually: `docker compose exec workspace npm run dev -- --host 0.0.0.0 --port 5173`

3. **Check container status / logs**:
   - `docker compose ps`
   - `docker compose logs -f <service_name>`
