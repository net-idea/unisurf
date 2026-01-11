# Helper scripts reference

This project contains a set of small shell helpers under `unisurf.de/`.

## Development entrypoints

### `develop.sh`

Interactive dev helper.

- **Option 1 (local mode)**: host PHP/Composer/Yarn + MariaDB via Docker.
- **Option 2 (docker mode)**: full Docker compose dev stack.

Usage:

```bash
./develop.sh      # Attached mode (logs shown, Ctrl+C stops stack)
./develop.sh -d   # Detached mode (background)
```

### `docker-start.sh`

Starts the full Docker dev stack.

- brings up containers
- waits for DB readiness
- installs Composer/Yarn dependencies
- clears + warms cache
- optionally runs Doctrine migrations
- starts the Encore dev-server via the `node` service

## Running commands in containers

### `docker-php.sh`

Run PHP/Symfony commands in the `php` service.

Examples:

```bash
./php.sh -v
./php.sh bin/console about
./php.sh bin/console doctrine:migrations:migrate --no-interaction
```

### `docker-yarn.sh`

Run Yarn/Encore commands in the `node` service.

Examples:

```bash
./yarn.sh install
./yarn.sh encore dev --watch
./yarn.sh encore production
```

## Diagnostics / maintenance

### `docker-list.sh`

Single source of truth for compose file selection and the service list.

```bash
./docker-list.sh
./docker-list.sh --services
./docker-list.sh --compose-args
```

### `docker-test.sh`

Checks whether the expected services are running and whether the DB responds to a trivial query.

```bash
./docker-test.sh
```

### `docker-delete.sh`

Stops and removes the current project stack and deletes local DB data folders.

⚠️ Deletes database data.

### `docker-stop.sh`

Stops _all_ Docker containers on your machine.

⚠️ Not project-specific.

### `database-backup.sh`

Create or restore database backups:

```bash
# Create backup
./database-backup.sh                              # uses DB from .env
./database-backup.sh --engine mariadb|postgres    # override engine
./database-backup.sh --output-dir /path/to/dir    # custom output directory

# Restore backup
./database-backup.sh --restore backups/unisurf-mariadb-20260111-141641.sql
./database-backup.sh --restore FILE --engine postgres
```

⚠️ Restore will prompt for confirmation before overwriting the database.

### `database-init.sh`

Initialize the database and run migrations. Optional flags:

```bash
./database-init.sh --engine mariadb|postgres   # default from DB
./database-init.sh --local                     # use local PHP/DB (DATABASE_URL)
./database-init.sh --reset                     # compose mode only: drop volumes/data, restart DB+php, rerun migrations
```

### `database-migrate.sh`

Thin wrapper that calls `database-init.sh`.
