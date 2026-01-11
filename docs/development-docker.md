# Docker development

Use this mode if you want a reproducible environment without installing PHP/Composer/Node/Yarn locally.

## Quick start

```bash
./develop.sh
# choose: 2) Docker Compose (full dev stack)
```

Or directly:

```bash
./docker-start.sh
```

The stack starts (depending on `DB`):

- `php` (Symfony)
- `nginx` (web)
- `node` (Webpack Encore dev-server)
- `database` (MariaDB) **or** `postgres`
- `mailer` (Mailpit)
- `adminer`, `phpmyadmin`

## URLs / Ports

Default URLs:

- App: `http://localhost:8000`
- Assets (Encore dev-server): `http://localhost:8080`
- Mailpit: `http://localhost:8025`
- Adminer: `http://localhost:8091`
- phpMyAdmin: `http://localhost:8092`

## Common tasks (Docker)

### Run Symfony commands

Use `docker-php.sh`:

```bash
./php.sh bin/console cache:clear
./php.sh bin/console doctrine:migrations:migrate --no-interaction
```

### Run Yarn / Encore

Use `docker-yarn.sh` (runs in the `node` service):

```bash
./yarn.sh install
./yarn.sh encore dev --watch
./yarn.sh encore production
```

### Check stack health

```bash
./docker-test.sh
```

### List compose files/services used

```bash
./docker-list.sh
./docker-list.sh --services
./docker-list.sh --compose-args
```

## Configuration

Use `.env.local` for machine-specific overrides.

Key variables:

- `APP_NAME` (compose project name)
- `DB` (`mariadb` or `postgres`)
- `DATABASE_URL` (Doctrine DSN used by the PHP container)

## Stopping / deleting

Stop this project stack:

```bash
docker compose -p "${APP_NAME:-unisurf}" down
```

Delete stack + volumes (⚠️ deletes DB data):

```bash
./docker-delete.sh
```

Note: `docker-stop.sh` stops _all_ Docker containers on your machine.
