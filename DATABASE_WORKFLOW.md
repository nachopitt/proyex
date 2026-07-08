# Database Schema Sync and Migration Workflow

This document records the design decisions, resolved issues, and the final automated workflow for comparing your MySQL Workbench model with the active local database and generating Laravel migrations.

---

## 1. Resolved Issues

### A. SSL Export Errors (Resolved in `db-commands`)
* **Problem**: Running `php artisan db:export` inside the container threw TLS/SSL handshake errors because the MariaDB CLI client defaults to demanding encrypted connections to MySQL 8.
* **Resolution**: Officially updated the `nachopitt/db-commands` package to accept and support the `--skip-ssl` flag natively so that no manual file modifications are required.

### B. Collation Mismatches (Resolved in `docker-compose.yml`)
* **Problem**: MySQL 8 default schema/database collation is `utf8mb4_0900_ai_ci`, but Laravel migrations create tables with `utf8mb4_unicode_ci`. This collation mismatch causes Atlas to attempt a schema-level `ALTER SCHEMA` command, which fails because Atlas restricts schema-level mutations when scoped to a single database schema.
* **Resolution**: Configured the MySQL container command in [docker-compose.yml](docker-compose.yml) to default globally to `--character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci`. This guarantees both the live database and any created dev sandboxes inherit the correct collation from the start.

---

## 2. The Final Architecture: Atlas CLI

To bypass all the parser limitations of external tools, we integrated **Atlas CLI** ([atlasgo.io](https://atlasgo.io)). Atlas parses MySQL 8 structures natively and compares the live database directly against your SQL DDL file without needing `mysqldump` exports.

### Docker Integration
The Atlas CLI is built directly into the [docker/php/Dockerfile](docker/php/Dockerfile) development stage. It uses an isolated alpine builder stage to download the binary via the official script, then copies it into the `workspace` stage:

```dockerfile
# Stage: atlas_builder
FROM alpine:latest AS atlas_builder
RUN apk add --no-cache curl
RUN curl -sSf https://atlasgo.sh | sh

# Workspace Stage
FROM dev AS workspace
COPY --from=atlas_builder /usr/local/bin/atlas /usr/local/bin/atlas
```

---

## 3. The Main Automation Workflow (Atlas)

### Step 0: Pre-requisites (Run Once)
Atlas requires a temporary clean database as a calculation sandbox to normalize your schema. Create this once on your database container:

```bash
docker compose exec db mysql -uroot -proot_password -e "CREATE DATABASE IF NOT EXISTS proyex_dev;"
```

### The Synchronization Commands

Run these steps when you make changes to your MySQL Workbench model and export it to [database_model/proyex.sql](database_model/proyex.sql):

#### 1. Generate the SQL Diff File
Atlas compares your active database (`--from`) against the Workbench model file (`--to`) using the sandbox (`--dev-url`), while ignoring Laravel's system tables (`--exclude`):

```bash
docker compose exec workspace atlas schema diff \
  --from "mysql://proyex:proyex@db:3306/proyex" \
  --to "file://database_model/proyex.sql" \
  --dev-url "mysql://root:root_password@db:3306/proyex_dev" \
  --exclude "cache" \
  --exclude "cache_locks" \
  --exclude "failed_jobs" \
  --exclude "job_batches" \
  --exclude "jobs" \
  --exclude "migrations" \
  --exclude "password_reset_tokens" \
  --exclude "sessions" \
  > database_model/diff.sql
```

#### 2. Import the Diff as a Laravel Migration
Feed the generated [database_model/diff.sql](database_model/diff.sql) file into your Artisan command:

```bash
docker compose exec workspace php artisan migrate:import --squash database_model/diff.sql
```

#### 3. Clean up the Temporary Diff File
```bash
rm database_model/diff.sql
```

---

## 4. Alternative Workflows (For Reference)

In case you need to fall back to older parsing engines, you can use these workflows.

### Alternative A: Using `schemalex`

This tool compares two static SQL dump files.

#### Known Limitations:
* Does not support the `FULLTEXT KEY` syntax produced by `mysqldump` (requires replacing with `FULLTEXT INDEX`).
* Does not support MySQL 8 index visibility settings like `VISIBLE` / `INVISIBLE`.

#### Commands:
```bash
# 1. Export current schema
docker compose exec workspace php artisan db:export --skip-ssl > database_model/current_schema.sql

# 2. Clean syntax using sed replacements
sed -i 's/FULLTEXT KEY/FULLTEXT INDEX/g' database_model/current_schema.sql
sed -i -E 's/ (VISIBLE|INVISIBLE)//gI' database_model/current_schema.sql
sed -i -E 's/ (VISIBLE|INVISIBLE)//gI' database_model/proyex.sql

# 3. Generate SQL diff using schemalex
docker compose exec workspace schemalex -o database_model/diff.sql database_model/current_schema.sql database_model/proyex.sql

# 4. Import diff to migration
docker compose exec workspace php artisan migrate:import database_model/diff.sql

# 5. Clean up
rm database_model/current_schema.sql database_model/diff.sql
```

---

### Alternative B: Using `camcima/php-mysql-diff`

This tool parses two static SQL files using regular expressions.

#### Known Limitations:
* **Symfony 7 Compatibility**: Requires manual changes to the `: int` return type hint on the `execute()` methods of [vendor/camcima/php-mysql-diff/src/Command/MigrateCommand.php](vendor/camcima/php-mysql-diff/src/Command/MigrateCommand.php) and [vendor/camcima/php-mysql-diff/src/Command/DiffCommand.php](vendor/camcima/php-mysql-diff/src/Command/DiffCommand.php).
* **PCRE Backtrack Limit**: Multi-line constraint lists trigger PHP's backtrack limit (`PREG_BACKTRACK_LIMIT_ERROR`), requiring the `-d pcre.backtrack_limit=10000000` flag.
* **Empty Charsets**: Workbench tables without explicit charsets generate invalid `DEFAULT CHARSET=;` SQL (must be cleaned up manually or with `sed`).

#### Commands:
```bash
# 1. Export current schema
docker compose exec workspace php artisan db:export --skip-ssl > database_model/current_schema.sql

# 2. Compare files using camcima with increased PCRE limit and table ignore list
docker compose exec workspace php -d pcre.backtrack_limit=10000000 vendor/bin/php-mysql-diff migrate \
  database_model/current_schema.sql \
  database_model/proyex.sql \
  --ignore=database_model/ignore.list \
  -o database_model/diff.sql

# 3. Clean up empty charset generation bugs
sed -i 's/DEFAULT CHARSET=;//g' database_model/diff.sql

# 4. Import diff to migration
docker compose exec workspace php artisan migrate:import database_model/diff.sql

# 5. Clean up
rm database_model/current_schema.sql database_model/diff.sql
```
