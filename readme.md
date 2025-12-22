# UniSurf - Web Hosting and Infrastructure Development

[![PHP CI](https://github.com/adamibrom/unisurf/workflows/PHP%20CI/badge.svg)](https://github.com/adamibrom/unisurf/actions/workflows/php.yml)
[![Node CI](https://github.com/adamibrom/unisurf/workflows/Node%20CI/badge.svg)](https://github.com/adamibrom/unisurf/actions/workflows/node.yml)

A modern Symfony web application for the web hosting and infrastructure development provider UniSurf.

- Framework: Symfony 7.3 (PHP 8.3+)
- Frontend: Webpack Encore, Stimulus, Bootstrap 5, Twig
- Database: MariaDB (SQLite defaults also supported)
- Tooling: Composer, Yarn, Docker and Docker Compose

## 📁 Project structure

```
unisurf.de/
├── develop.sh                    # Local dev helper (installs deps, builds, runs dev services)
├── deploy.sh                     # Production deploy helper (builds, installs, migrates, warms cache)
├── assets/
│   ├── app.ts                    # TS entry (Webpack Encore)
│   ├── bootstrap.ts              # Stimulus bootstrap (TS)
│   ├── controllers/              # Stimulus controllers
│   ├── controllers.json          # Stimulus bridge entry
│   ├── scripts/
│   │   ├── main.ts               # Main UI behaviors (TS)
│   │   └── theme-toggle.ts       # Theme handling (TS)
│   └── styles/
│       ├── app.css               # App-specific styles
│       ├── theme.css             # Shared theme styles
│       ├── theme-light.css       # Light theme overrides
│       └── theme-dark.css        # Dark theme overrides
├── config/                       # Symfony configuration
├── docs/                         # Project docs
│   ├── docker.md                 # Docker installation & usage
│   ├── symfony.md                # Symfony commands & troubleshooting
│   └── database.md               # Database troubleshooting
├── public/                       # Web root
│   ├── index.php                 # Front controller
│   ├── build/                    # Compiled assets (generated)
│   └── bundles/
├── src/
│   ├── Controller/
│   │   ├── HomeController.php    # Homepage
│   │   └── ContactController.php # Contact form page
│   ├── Entity/
│   │   ├── FormContactEntity.php        # Contact form entity
│   │   └── FormSubmissionMetaEntity.php # Form submission metadata
│   ├── Form/
│   │   └── FormContactType.php          # Contact form type
│   ├── Repository/
│   │   └── FormContactRepository.php    # Contact form repository
│   └── Service/
│       └── FormContactService.php       # Contact form service
├── templates/
│   ├── base.html.twig                   # Base layout with theme support
│   ├── _partials/
│   │   └── navbar.html.twig             # Navigation with theme switcher
│   ├── home/
│   │   └── index.html.twig              # Modern homepage
│   └── contact/
│       └── index.html.twig              # Contact form page
├── migrations/
├── vendor/                              # Composer deps (generated)
├── var/                                 # Cache & logs (generated)
├── composer.json
├── package.json
└── readme.md
```

## ✅ Local development (recommended)

### Prerequisites

- PHP 8.3+
- Composer
- Node.js 20+ and Yarn
- Symfony CLI (optional, for local web server)
- Docker Desktop (for MariaDB container in local mode)

### Quick start

Run the helper and choose your environment:

```bash
./develop.sh
```

- Option 1: **Local (host tools)** — installs Composer/Yarn locally, starts MariaDB via Docker (`docker-compose.mariadb.yml` + `docker-compose.mariadb.dev.yml`), runs `yarn encore dev --watch`, and launches PHP's built-in server on `http://127.0.0.1:8000`.
- Option 2: **Docker Compose (dev stack)** — brings up PHP, Nginx, Node Encore watcher, MariaDB, and Mailpit via compose.

If you prefer to run local steps manually:

```bash
# 1) Install dependencies
yarn install
composer install

# 2) Clear cache (dev)
php bin/console cache:clear

# 3) Build assets in watch mode
yarn encore dev --watch

# 4) Start a local web server (one of)
symfony server:start --no-tls --port=8000
# or
php -S 127.0.0.1:8000 -t public
```

Open the app:

```
http://localhost:8000
```

### Docker dev stack (manual)

```bash
docker compose -p unisurf \
  -f docker-compose.yaml \
  -f docker-compose.dev.yaml \
  -f docker-compose.mariadb.yml \
  -f docker-compose.mariadb.dev.yml \
  up -d --build
```

Services:

- App: `http://localhost:${NGINX_PORT}`
- Encore dev server: `http://localhost:${NODE_PORT}`
- Mailpit: `http://localhost:${MAILER_WEB_PORT}`
- MariaDB: `localhost:${DB_PORT}` (version 11.4.3)

### Production (compose)

`docker-compose.yaml` is prod-oriented (APP_ENV=prod, no dev mounts). Build assets before deploy, then run:

```bash
docker compose -p unisurf -f docker-compose.yaml -f docker-compose.mariadb.yml up -d --build
```

## ✅ Environment variables

All configuration is via environment variables. Typical keys:

- APP_ENV: dev | prod (default: dev)
- APP_SECRET: random string (generate via `php bin/console regenerate-app-secret`)
- DEFAULT_URI: base URL used for URL generation in CLI contexts (e.g. http://localhost)
- LOCK_DSN: lock store DSN (default in dev: `flock`). Examples: `flock`, `semaphore`, `redis://localhost:6379`
- DATABASE_URL: Doctrine DSN
  - SQLite (default): `DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"`
  - MariaDB/MySQL: `DATABASE_URL="mysql://user:pass@127.0.0.1:3306/db?serverVersion=11.4.3-MariaDB&charset=utf8mb4"`
  - Postgres: `DATABASE_URL="postgresql://user:pass@127.0.0.1:5432/db?serverVersion=16&charset=utf8"`
- MESSENGER_TRANSPORT_DSN: default `doctrine://default?auto_setup=0` (use `sync://` for simple dev)
- Mail settings (compose into MAILER_DSN): MAIL_SCHEME, MAIL_HOST, MAIL_ENCRYPTION, MAIL_PORT, MAIL_USER, MAIL_PASSWORD

Security: Do not commit production secrets. Prefer real env vars or Symfony Secrets Vault for prod.

---

## ⚡️ Command Summary

| Task                   | Local Command                               | Docker Command Example                                                         |
| ---------------------- | ------------------------------------------- | ------------------------------------------------------------------------------ |
| Start dev server       | symfony server:start                        | docker compose -p unisurf up -d                                                |
| Run Symfony console    | php bin/console <cmd>                       | docker compose -p unisurf exec php php bin/console <cmd>                       |
| Update DB schema       | php bin/console doctrine:schema:update      | docker compose -p unisurf exec php php bin/console doctrine:schema:update      |
| List contacts (custom) | php bin/console app:list:contacts           | docker compose -p unisurf exec php php bin/console app:list:contacts           |
| Create migration       | php bin/console make:migration              | docker compose -p unisurf exec php php bin/console make:migration              |
| Run migrations         | php bin/console doctrine:migrations:migrate | docker compose -p unisurf exec php php bin/console doctrine:migrations:migrate |
| Run pipeline           | ./pipeline.sh                               | docker compose -p unisurf exec php ./pipeline.sh                               |

---

## 🐳 Using Symfony & Doctrine Commands in Docker

To run Symfony or Doctrine commands inside your Docker PHP container, use:

```bash
docker compose -p unisurf exec php php bin/console <command>
```

**Examples:**

- Update DB schema:
  ```bash
  docker compose -p unisurf exec php php bin/console doctrine:schema:update
  ```
- List contacts:
  ```bash
  docker compose -p unisurf exec php php bin/console app:list:contacts
  ```
- Create migration file:
  ```bash
  docker compose -p unisurf exec php php bin/console make:migration
  ```
- Run migrations:
  ```bash
  docker compose -p unisurf exec php php bin/console doctrine:migrations:migrate
  ```

---

## 🗄️ Database Backends & Migrations

Supported SQL backends:

- **SQLite** (default, file-based, easy for dev)
- **MariaDB/MySQL** (recommended for production)
- **PostgreSQL** (fully supported)

**Configure backend via `DATABASE_URL` in `.env` or environment:**

- SQLite:
  ```env
  DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
  ```
- MariaDB/MySQL:
  ```env
  DATABASE_URL="mysql://user:pass@127.0.0.1:3306/db?serverVersion=11.4.3-MariaDB&charset=utf8mb4"
  ```
- PostgreSQL:
  ```env
  DATABASE_URL="postgresql://user:pass@127.0.0.1:5432/db?serverVersion=16&charset=utf8"
  ```

**Creating and running migrations:**

1. **Create migration file (after changing entities):**
   ```bash
   php bin/console make:migration
   # or in Docker:
   docker compose -p unisurf exec php php bin/console make:migration
   ```
2. **Review migration file in `migrations/` folder.**
3. **Run migrations:**
   ```bash
   php bin/console doctrine:migrations:migrate
   # or in Docker:
   docker compose -p unisurf exec php php bin/console doctrine:migrations:migrate
   ```

**Notes:**

- You can switch backends by changing `DATABASE_URL` and restarting containers.
- Migrations are backend-agnostic; Doctrine generates SQL for your configured DB.
- For production, always backup your database before running migrations.

---

## 🧹 Code Quality & Linting

The project includes several linting and formatting tools to ensure code quality:

### Run all linters (recommended)

```bash
./lint.sh
```

This aggregate script runs:

- CSS/SCSS linting with auto-fix (Stylelint)
- TypeScript type checking
- Twig template linting
- PHP code formatting (PHP-CS-Fixer)

### Individual linting commands

**CSS/SCSS (Stylelint):**

```bash
# Check only
yarn lint:css

# Auto-fix issues
yarn lint:css:fix
```

**TypeScript type checking:**

```bash
# One-time check
yarn tsc:check

# Watch mode (continuous)
yarn tsc:watch
```

**Twig templates:**

```bash
php bin/console lint:twig templates
```

**PHP code formatting (PHP-CS-Fixer):**

```bash
./php-cs-fixer.sh
```

This installs PHP-CS-Fixer locally to `php-cs-fixer/` directory and runs formatting on your PHP code.

## 🧪 Testing

### PHP Unit Tests

Run the full PHPUnit test suite:

```bash
./phpunit.sh
```

Or directly:

```bash
./vendor/bin/phpunit tests
```

### Frontend Build Verification

Verify that all frontend assets compile without errors:

```bash
# Development build
yarn dev

# Production build (includes optimizations)
yarn build
```

### Complete Quality Check

For a full quality check before committing or deploying, run:

```bash
# 1. Run all linters
./lint.sh

# 2. Run tests
./phpunit.sh

# 3. Verify production build
yarn build
```

## 🛠 Helper scripts

### develop.sh

Local and Docker dev helper that:

- Prompts for environment: local host tools + MariaDB via Docker, or full Docker dev stack
- Installs dependencies (Yarn and Composer) in local mode
- Clears Symfony cache (dev)
- Builds front-end assets (Encore watch)
- Starts Webpack Encore watch and PHP dev server (local) or Docker dev stack with node watcher

Usage:

```bash
./develop.sh
```

Notes:

- Local mode requires Node/Yarn, PHP and Composer available on your machine; Docker is still required for MariaDB.
- Docker mode uses compose files: `docker-compose.yaml`, `docker-compose.dev.yaml`, `docker-compose.mariadb.yml`, `docker-compose.mariadb.dev.yml`.
- Press Ctrl+C to stop local background processes; use `docker compose -p unisurf down` to stop the Docker stack.

### deploy.sh

Production deployment helper that:

- Ensures production env (APP_ENV=prod)
- Installs Node deps, builds assets (prod)
- Installs Composer deps (no-dev, optimized)
- Runs database migrations (can be skipped)
- Clears and warms Symfony cache (prod)

Usage:

```bash
# Default (runs migrations)
./deploy.sh

# Skip migrations
SKIP_MIGRATIONS=true ./deploy.sh

# Skip composer auto-scripts (if you need to)
SKIP_COMPOSER_AUTOSCRIPTS=true ./deploy.sh
```

### pipeline.sh

Runs the full build, test, and deployment pipeline (lint, tests, build, migrations, cache warmup, etc). Use for CI/CD or local pre-deploy checks.

Usage:

```bash
./pipeline.sh
# or in Docker:
docker compose -p unisurf exec php ./pipeline.sh
```

## 🧰 Symfony commands

Moved to:

- docs/symfony.md

## 🆘 Troubleshooting

Troubleshooting has been split by topic:

- Docker: docs/docker.md
- Symfony: docs/symfony.md
- Database: docs/database.md

## 📄 License

See [license](license) for details.

## 🤝 Contact

For questions or issues, please open a GitHub issue in this repository.
