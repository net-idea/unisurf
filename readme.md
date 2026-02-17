# UniSurf

> Language: English · German version: [readme.de.md](readme.de.md)

**Unique Surfing** – Your specialist for digital infrastructure and IT services.

UniSurf provides managed hosting, webmaster services, server administration, web development, and IT consulting. We support businesses with reliable web infrastructure, from managed servers to custom web applications – personal, competent, and pragmatic.

🌐 **Live:** https://unisurf.de/

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [Development Environment](#development-environment)
3. [Project Structure](#project-structure)
4. [Backend Development](#backend-development)
5. [Frontend Development](#frontend-development)
6. [Testing & Quality](#testing--quality)
7. [Deployment](#deployment)
8. [Documentation](#documentation)

---

## Getting Started

### Prerequisites

- Git
- Docker & Docker Compose v2
- PHP 8.3+ (for local development)
- Node.js 20+ & Yarn (for local development)
- Composer (for local development)

### Clone Repository

```bash
git clone git@github.com:bassix/unisurf.git unisurf.de
cd unisurf.de

# Initialize submodules (DevTools + web-base)
git submodule update --init --recursive
```

### First-Time Setup

```bash
# Run DevTools installer (creates .env, directories)
.devtools/install.sh

# Review and adjust .env if needed
nano .env
```

---

## Development Environment

All development scripts are located in `.devtools/`. Run them from the project root.

### Option 1: Full Docker Stack (Recommended)

```bash
# Start development environment
.devtools/develop.sh
# Choose: 2) Docker Compose (full dev stack)

# Or start directly
.devtools/docker-start.sh
```

**Available Services:**

| Service    | URL                      |
|------------|--------------------------|
| App        | http://localhost:8000    |
| Assets     | http://localhost:8080    |
| Mailpit    | http://localhost:8025    |
| Adminer    | http://localhost:8091    |
| phpMyAdmin | http://localhost:8092    |

### Option 2: Local Tools + Docker DB

```bash
.devtools/develop.sh
# Choose: 1) Local (host tools) + MariaDB via Docker
```

App: http://127.0.0.1:8000

### DevTools Scripts Reference

```bash
# Docker Management
.devtools/docker-start.sh       # Start Docker stack
.devtools/docker-stop.sh        # Stop Docker stack
.devtools/docker-list.sh        # List services and compose files
.devtools/docker-test.sh        # Health checks
.devtools/docker-delete.sh      # Delete stack (including volumes)

# Database
.devtools/database-init.sh      # Initialize database
.devtools/database-migrate.sh   # Run migrations
.devtools/database-backup.sh    # Create backup
.devtools/database-backup.sh --restore <file>  # Restore backup

# Utilities
.devtools/php.sh <command>      # Run PHP in container
.devtools/yarn.sh <command>     # Run Yarn in container
.devtools/clear-cache.sh        # Clear Symfony cache
```

### Web-Base Bundle

This project uses the `net-idea/web-base` bundle for common functionality. It's located in:

```
vendor/net-idea/web-base/
```

For local development with the bundle source:

```bash
# Clone web-base for development
.devtools/develop-web-base.sh
# This clones web-base to packages/ and configures composer.local.json
```

---

## Project Structure

```
unisurf.de/
├── .devtools/              # Development tools (Git submodule)
│   ├── docker/             # Docker configuration
│   ├── *.sh                # Helper scripts
│   └── readme.md           # DevTools documentation
├── assets/                 # Frontend assets
│   ├── controllers/        # Stimulus controllers
│   ├── styles/             # SCSS stylesheets
│   └── scripts/            # TypeScript files
├── config/                 # Symfony configuration
├── content/                # Markdown content (legal pages)
├── migrations/             # Doctrine migrations
├── public/                 # Web root
├── src/                    # PHP source code
│   ├── Controller/         # HTTP controllers
│   ├── Entity/             # Doctrine entities
│   ├── Form/               # Symfony forms
│   ├── Repository/         # Doctrine repositories
│   └── Services/           # Business logic
├── templates/              # Twig templates
│   └── pages/              # Page templates
├── tests/                  # PHPUnit tests
├── translations/           # Translation files
└── deploy.sh               # Production deployment script
```

---

## Backend Development

### Tech Stack

- **Framework:** Symfony 8 (PHP 8.3+)
- **Database:** MariaDB (default) or PostgreSQL
- **ORM:** Doctrine

### Creating a Controller

```bash
.devtools/php.sh bin/console make:controller MyController
```

Controllers go in `src/Controller/`. Follow these conventions:
- Use PHP 8 Attributes for routing: `#[Route('/path', name: 'app_name')]`
- Inject dependencies via constructor
- Keep controllers thin, move logic to Services

### Creating an Entity

```bash
.devtools/php.sh bin/console make:entity MyEntity
```

Entities go in `src/Entity/`. Always use:
- `declare(strict_types=1);`
- PHP 8 Attributes for ORM: `#[ORM\Entity]`, `#[ORM\Column]`

### Creating a Migration

```bash
.devtools/php.sh bin/console make:migration
.devtools/database-migrate.sh
```

### Symfony Commands

```bash
# List all commands
.devtools/php.sh bin/console list

# Clear cache
.devtools/php.sh bin/console cache:clear

# Debug routes
.devtools/php.sh bin/console debug:router
```

---

## Frontend Development

### Tech Stack

- **CSS:** Bootstrap 5, SCSS
- **JavaScript:** TypeScript, Stimulus
- **Build:** Webpack Encore

### Asset Structure

```
assets/
├── app.ts                  # Main entry point
├── bootstrap.ts            # Bootstrap initialization
├── controllers/            # Stimulus controllers
├── scripts/                # TypeScript modules
└── styles/
    ├── app.scss            # Main stylesheet
    └── components/         # Component styles
```

### Building Assets

```bash
# Development (watch mode)
.devtools/yarn.sh encore dev --watch

# Production build
.devtools/yarn.sh encore production

# Or via Docker node service (auto-watches)
.devtools/docker-start.sh
```

### Creating a Stimulus Controller

Create `assets/controllers/my_controller.ts`:

```typescript
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    console.log('Controller connected');
  }
}
```

Use in Twig:

```twig
<div data-controller="my"></div>
```

---

## Testing & Quality

### Running Tests

```bash
# PHPUnit tests
.devtools/phpunit.sh

# Or directly
.devtools/php.sh bin/phpunit
```

### Code Quality

```bash
# Lint everything
.devtools/lint.sh

# PHP Code Style (CS Fixer)
.devtools/php-cs-fixer.sh

# TypeScript type check
.devtools/yarn.sh tsc:check

# Stylelint
.devtools/yarn.sh lint:fix

# Prettier
.devtools/yarn.sh prettier:fix
```

### Full CI/CD Pipeline (Local)

```bash
# Run all checks (same as GitHub Actions)
.devtools/pipeline.sh

# Continue on errors
.devtools/pipeline.sh --continue-on-error

# Verbose output
.devtools/pipeline.sh --verbose
```

The pipeline runs:
- Composer validate
- PHP CS Fixer
- PHPStan (static analysis)
- PHPUnit tests
- Twig linting
- TypeScript checks
- Stylelint
- Asset build
- Prettier

---

## Deployment

### Production Deployment

```bash
# From project root (not .devtools!)
./deploy.sh
```

The deploy script:
1. Installs Composer dependencies (--no-dev)
2. Installs Yarn dependencies
3. Builds production assets
4. Runs database migrations
5. Clears and warms cache

### Skip Migrations

```bash
SKIP_MIGRATIONS=true ./deploy.sh
```

### Manual Deployment Steps

```bash
composer install --no-dev --optimize-autoloader
yarn install --frozen-lockfile
yarn encore production
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

---

## Documentation

- **DevTools:** `.devtools/readme.md`
- **Development:** `docs/development-local.md`, `docs/development-docker.md`
- **Scripts:** `docs/scripts.md`
- **Database:** `docs/database.md`
- **Secrets:** `docs/secrets.md`

---

## CI/CD

[![PHP CI](https://github.com/bassix/unisurf/actions/workflows/php.yml/badge.svg?branch=main)](https://github.com/bassix/unisurf/actions/workflows/php.yml)
[![Node CI](https://github.com/bassix/unisurf/actions/workflows/node.yml/badge.svg?branch=main)](https://github.com/bassix/unisurf/actions/workflows/node.yml)

GitHub Actions workflows:
- **PHP CI:** Composer, CS Fixer, PHPStan, PHPUnit, Twig lint
- **Node CI:** TypeScript, Stylelint, Webpack build, Prettier

---

## Contributing

1. Fork the repository
2. Create a feature branch
3. Run `.devtools/pipeline.sh` before committing
4. Submit a pull request

---

## License

MIT – see [license](license)

---

## Contact

Questions or project inquiries: https://unisurf.de/kontakt
