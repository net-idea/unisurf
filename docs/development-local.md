# Local development

Use this mode if you prefer running PHP/Composer/Node/Yarn on your host, while still using Docker for the database.

## Quick start

```bash
./develop.sh
# choose: 1) Local (host tools) + MariaDB via Docker
```

This will:

- start MariaDB via Docker Compose (dev DB compose files)
- install Composer and Yarn dependencies on your host
- clear/warm Symfony cache
- optionally run migrations
- start:
  - PHP built-in server: `http://127.0.0.1:8000`
  - Encore dev-server: `http://127.0.0.1:8080`

## Requirements

- PHP 8.3+
- Composer
- Node.js 20+
- Yarn
- Docker Desktop (for MariaDB container)

## Common tasks (local)

```bash
php bin/console cache:clear
php bin/console doctrine:migrations:migrate

yarn encore dev --watch
```

## Database notes

In local mode the DB still runs in Docker. Your `DATABASE_URL` must point to the exposed database port, typically:

- MariaDB: `mysql://...@127.0.0.1:3306/...`

Avoid `localhost` for MySQL/MariaDB DSNs on macOS/Linux if you see `SQLSTATE[HY000] [2002] No such file or directory`.
Use `127.0.0.1` to force TCP instead of a unix socket.
