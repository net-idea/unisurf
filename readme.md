# UniSurf

Symfony application with Webpack Encore assets.

- Backend: Symfony (PHP)
- Frontend: Webpack Encore (TS/CSS) + Twig
- Database: MariaDB (default) or PostgreSQL
- Dev tooling: Docker Compose and a set of helper scripts

## Quick start

You have two supported development setups:

### 1) Local development (host tools + DB via Docker)

Runs PHP/Composer/Node/Yarn on your machine, but starts the database via Docker.

```bash
cd unisurf.de
./develop.sh
# choose: 1) Local (host tools) + MariaDB via Docker
```

App: `http://127.0.0.1:8000`

### 2) Docker development (full stack via Docker Compose)

Runs PHP, Node/Encore, Nginx, DB and Mailpit in containers.

```bash
cd unisurf.de
./develop.sh      # Attached mode (logs shown, Ctrl+C stops stack)
./develop.sh -d   # Detached mode (background)
# choose: 2) Docker Compose (full dev stack)

# or:
./docker-start.sh
```

App: `http://localhost:8000`

## Common commands

### Symfony / PHP (Docker)

```bash
./docker-php.sh bin/console about
./docker-php.sh bin/console doctrine:migrations:migrate --no-interaction
```

### Yarn / Encore (Docker)

```bash
./docker-yarn.sh encore dev --watch
```

### Database init / migrations

```bash
./database-init.sh           # starts DB (compose), waits, runs migrations
./database-init.sh --reset   # drop volumes/data (compose), restart DB+php, rerun migrations
```

## Documentation

Detailed docs are under `docs/`:

- `docs/README.md`
- Local dev: `docs/development-local.md`
- Docker dev: `docs/development-docker.md`
- Helper scripts: `docs/scripts.md`

## Repo structure

Application code lives in `unisurf.de/`.

## License

See `license`.
