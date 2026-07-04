# Project-Specific Agent Rules for Proyex

> [!IMPORTANT]
> **Synchronization Notice**: When updating or modifying these rules, please make sure to keep them synchronized with the GitHub Copilot instructions at `.github/copilot-instructions.md`.

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

4. **Docker Command Execution (Non-Interactive TTY / Background tasks)**:
   - **Rule**: Whenever you run terminal commands involving `docker compose exec` inside background tasks, you **MUST** pass the `-T` flag (e.g., `docker compose exec -T workspace ...`).
   - **Reason**: The agent's background process execution environment is a non-interactive shell with no pseudo-TTY allocated. Running `docker compose exec` without `-T` will fail with the error: `the input device is not a TTY`.
   - **Examples**:
     - *Incorrect*: `docker compose exec workspace npm run lint`
     - *Correct*: `docker compose exec -T workspace npm run lint`
     - *Correct*: `docker compose exec -T workspace php artisan test`
