# UniSurf

> Language: English · German version: [readme.de.md](readme.de.md)

UniSurf provides reliable, personal, and competent digital infrastructure: managed hosting, web environments, and development — from a single source. This repository contains the UniSurf website and its management system.

In short: we design, run, and maintain robust web infrastructure — from managed servers and web hosting to databases, email services, and secure integrations. Businesses get a direct line, transparent processes, and pragmatic, right‑sized solutions.

Live website: https://unisurf.de/

## Overview for visitors

The site presents our services clearly and guides you to the relevant sections:

- Home (overview and value proposition)
- Services (Managed Servers & Administration, Web hosting, Networking, Data & Storage, Cloud Integration & Consulting, AI & Automation, IT Security)
- Hosting (target groups, scope of services, quality promise)
- Development (websites/portals, e‑commerce, internal networking, APIs & web applications, how we work)
- Contact (contact form with GDPR notes)
- Legal (imprint, privacy policy)

All pages are responsive, accessible, and support light/dark themes via a theme switcher.

## Why UniSurf?

- Direct line: personal contact instead of ticket limbo.
- Transparent processes: understandable workflows, clear responsibilities.
- Stability & maintainability: focus on reliable operations and clean upgrades.
- Flexibility: from managed servers to tailored development — all in one place.
- Security with common sense: solid standards, sensible hardening, and privacy.

## Features

- Managed hosting & infrastructure with a focus on stability and maintainability
- Web hosting & web environments, email service, database management
- Development of websites, portals, APIs, and internal applications
- Responsive design based on Bootstrap 5, clear typography, reusable components
- Light/dark theme switcher in the navigation
- Contact form with validation, spam protection (honeypots), and delivery via Symfony Mailer
- Legal content rendered from Markdown (imprint, privacy policy)
- Developer-friendly environment with Docker Compose, Adminer/phpMyAdmin, and Mailpit

## Tech stack

- Backend: Symfony 8 (PHP)
- Frontend: Twig, Webpack Encore (TypeScript/Sass), Bootstrap 5
- Database: MariaDB (default) or PostgreSQL
- Development: Docker Compose, Yarn, helper scripts

See `composer.json` and `package.json` for details.

---

## Quick start

Two recommended development setups are available.

### 1) Local (host tools, DB via Docker)

```bash
cd unisurf.de
./develop.sh
# choose: 1) Local (host tools) + MariaDB via Docker
```

App: `http://127.0.0.1:8000`

### 2) Full Docker (Docker Compose)

```bash
cd unisurf.de
./develop.sh      # Attached (logs in foreground)
./develop.sh -d   # Detached (background)
# choose: 2) Docker Compose (full dev stack)

# or directly:
./docker-start.sh
```

App: `http://localhost:8000`

### Services/tools in the Docker setup

- App: `http://localhost:8000`
- Assets (Encore dev server): `http://localhost:8080`
- Mailpit: `http://localhost:8025`
- Adminer: `http://localhost:8091`
- phpMyAdmin: `http://localhost:8092`

## Common commands

### Symfony / PHP (Docker)

```bash
./php.sh bin/console about
./php.sh bin/console doctrine:migrations:migrate --no-interaction
```

### Yarn / Encore (Docker)

```bash
./yarn.sh encore dev --watch
```

### Database init / migrations

```bash
./database-init.sh           # starts DB (compose), waits, runs migrations
./database-init.sh --reset   # drop volumes/data (compose), restart DB+php, run migrations again
```

## Pages & links

- Live: https://unisurf.de/
- Local routes during development (Docker/Local):
  - `/` (home)
  - `/services`
  - `/hosting`
  - `/entwicklung` (development)
  - `/kontakt` (contact)
  - `/impressum` (imprint)
  - `/datenschutz` (privacy)

## Documentation

Full documentation resides in `docs/`:

- [docs/readme.md](docs/readme.md)
- Local development: [docs/development-local.md](docs/development-local.md)
- Docker development: [docs/development-docker.md](docs/development-docker.md)
- Helper scripts: [docs/scripts.md](docs/scripts.md)
- Symfony commands: [docs/symfony.md](docs/symfony.md)
- Database: [docs/database.md](docs/database.md)
- Security/Secrets: [docs/secrets.md](docs/secrets.md)

## Project structure

The application lives in `unisurf.de/`. Page templates are under `templates/pages/*.html.twig`, styles under `assets/styles/`, and legal content under `content/*.md`.

## Contributing

- Pull requests welcome. Please use clear commits and short descriptions.
- Code style: use the existing lint/format scripts.
  - JavaScript/SCSS/Markup: `yarn lint` or `yarn lint:fix`
  - PHP: `./php-cs-fixer.sh` (if configured)
- Tests/Checks: in Docker setups, use `./docker-test.sh` for a quick health check.

## License

MIT. See `license`.

## Contact & support

Questions or project inquiries? We’re happy to hear from you:

- Contact form: https://unisurf.de/kontakt

---

Security note: please do not publish confidential credentials in issues or PRs. Use `.env.local` for local variables and see `docs/secrets.md`.
