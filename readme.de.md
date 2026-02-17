# UniSurf

> Sprache: Deutsch · English version: [readme.md](readme.md)

**Unique Surfing** – Ihr Spezialist für digitale Infrastruktur und IT-Dienstleistungen.

UniSurf bietet Managed Hosting, Webmaster Services, Server-Administration, Webentwicklung und IT-Beratung. Wir unterstützen Unternehmen mit zuverlässiger Web-Infrastruktur – von Managed Servern bis zu maßgeschneiderten Web-Anwendungen. Persönlich, kompetent und pragmatisch.

🌐 **Live:** https://unisurf.de/

---

## Inhaltsverzeichnis

1. [Erste Schritte](#erste-schritte)
2. [Entwicklungsumgebung](#entwicklungsumgebung)
3. [Projektstruktur](#projektstruktur)
4. [Backend-Entwicklung](#backend-entwicklung)
5. [Frontend-Entwicklung](#frontend-entwicklung)
6. [Tests & Qualität](#tests--qualität)
7. [Deployment](#deployment)
8. [Dokumentation](#dokumentation)

---

## Erste Schritte

### Voraussetzungen

- Git
- Docker & Docker Compose v2
- PHP 8.3+ (für lokale Entwicklung)
- Node.js 20+ & Yarn (für lokale Entwicklung)
- Composer (für lokale Entwicklung)

### Repository klonen

```bash
git clone git@github.com:bassix/unisurf.git unisurf.de
cd unisurf.de

# Submodules initialisieren (DevTools + web-base)
git submodule update --init --recursive
```

### Ersteinrichtung

```bash
# DevTools-Installer ausführen (erstellt .env, Verzeichnisse)
.devtools/install.sh

# .env prüfen und ggf. anpassen
nano .env
```

---

## Entwicklungsumgebung

Alle Entwicklungs-Scripts befinden sich in `.devtools/`. Ausführung vom Projekt-Root.

### Option 1: Docker Stack (Empfohlen)

```bash
# Entwicklungsumgebung starten
.devtools/develop.sh
# Auswählen: 2) Docker Compose (full dev stack)

# Oder direkt starten
.devtools/docker-start.sh
```

**Verfügbare Services:**

| Service    | URL                      |
|------------|--------------------------|
| App        | http://localhost:8000    |
| Assets     | http://localhost:8080    |
| Mailpit    | http://localhost:8025    |
| Adminer    | http://localhost:8091    |
| phpMyAdmin | http://localhost:8092    |

### Option 2: Lokale Tools + Docker DB

```bash
.devtools/develop.sh
# Auswählen: 1) Local (host tools) + MariaDB via Docker
```

App: http://127.0.0.1:8000

### DevTools Scripts Übersicht

```bash
# Docker-Verwaltung
.devtools/docker-start.sh       # Docker Stack starten
.devtools/docker-stop.sh        # Docker Stack stoppen
.devtools/docker-list.sh        # Services auflisten
.devtools/docker-test.sh        # Health Checks
.devtools/docker-delete.sh      # Stack löschen (inkl. Volumes)

# Datenbank
.devtools/database-init.sh      # Datenbank initialisieren
.devtools/database-migrate.sh   # Migrations ausführen
.devtools/database-backup.sh    # Backup erstellen
.devtools/database-backup.sh --restore <datei>  # Backup wiederherstellen

# Hilfsprogramme
.devtools/php.sh <befehl>       # PHP im Container ausführen
.devtools/yarn.sh <befehl>      # Yarn im Container ausführen
.devtools/clear-cache.sh        # Symfony Cache leeren
```

### Web-Base Bundle

Dieses Projekt nutzt das `net-idea/web-base` Bundle für gemeinsame Funktionalität. Es befindet sich in:

```
vendor/net-idea/web-base/
```

Für lokale Entwicklung mit dem Bundle-Quellcode:

```bash
# Web-base für Entwicklung klonen
.devtools/develop-web-base.sh
# Klont web-base nach packages/ und konfiguriert composer.local.json
```

---

## Projektstruktur

```
unisurf.de/
├── .devtools/              # Entwicklungs-Tools (Git Submodule)
│   ├── docker/             # Docker-Konfiguration
│   ├── *.sh                # Helper-Scripts
│   └── readme.md           # DevTools-Dokumentation
├── assets/                 # Frontend-Assets
│   ├── controllers/        # Stimulus-Controller
│   ├── styles/             # SCSS-Stylesheets
│   └── scripts/            # TypeScript-Dateien
├── config/                 # Symfony-Konfiguration
├── content/                # Markdown-Inhalte (Rechtliches)
├── migrations/             # Doctrine-Migrations
├── public/                 # Web-Root
├── src/                    # PHP-Quellcode
│   ├── Controller/         # HTTP-Controller
│   ├── Entity/             # Doctrine-Entities
│   ├── Form/               # Symfony-Formulare
│   ├── Repository/         # Doctrine-Repositories
│   └── Services/           # Business-Logik
├── templates/              # Twig-Templates
│   └── pages/              # Seiten-Templates
├── tests/                  # PHPUnit-Tests
├── translations/           # Übersetzungsdateien
└── deploy.sh               # Produktions-Deployment-Script
```

---

## Backend-Entwicklung

### Tech Stack

- **Framework:** Symfony 8 (PHP 8.3+)
- **Datenbank:** MariaDB (Standard) oder PostgreSQL
- **ORM:** Doctrine

### Controller erstellen

```bash
.devtools/php.sh bin/console make:controller MyController
```

Controller kommen nach `src/Controller/`. Konventionen:
- PHP 8 Attributes für Routing: `#[Route('/pfad', name: 'app_name')]`
- Dependencies per Constructor-Injection
- Controller schlank halten, Logik in Services auslagern

### Entity erstellen

```bash
.devtools/php.sh bin/console make:entity MyEntity
```

Entities kommen nach `src/Entity/`. Immer verwenden:
- `declare(strict_types=1);`
- PHP 8 Attributes für ORM: `#[ORM\Entity]`, `#[ORM\Column]`

### Migration erstellen

```bash
.devtools/php.sh bin/console make:migration
.devtools/database-migrate.sh
```

### Symfony-Befehle

```bash
# Alle Befehle auflisten
.devtools/php.sh bin/console list

# Cache leeren
.devtools/php.sh bin/console cache:clear

# Routen debuggen
.devtools/php.sh bin/console debug:router
```

---

## Frontend-Entwicklung

### Tech Stack

- **CSS:** Bootstrap 5, SCSS
- **JavaScript:** TypeScript, Stimulus
- **Build:** Webpack Encore

### Asset-Struktur

```
assets/
├── app.ts                  # Haupt-Einstiegspunkt
├── bootstrap.ts            # Bootstrap-Initialisierung
├── controllers/            # Stimulus-Controller
├── scripts/                # TypeScript-Module
└── styles/
    ├── app.scss            # Haupt-Stylesheet
    └── components/         # Komponenten-Styles
```

### Assets bauen

```bash
# Entwicklung (Watch-Modus)
.devtools/yarn.sh encore dev --watch

# Produktions-Build
.devtools/yarn.sh encore production

# Oder via Docker Node-Service (auto-watch)
.devtools/docker-start.sh
```

### Stimulus-Controller erstellen

Erstelle `assets/controllers/my_controller.ts`:

```typescript
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    console.log('Controller verbunden');
  }
}
```

Verwendung in Twig:

```twig
<div data-controller="my"></div>
```

---

## Tests & Qualität

### Tests ausführen

```bash
# PHPUnit-Tests
.devtools/phpunit.sh

# Oder direkt
.devtools/php.sh bin/phpunit
```

### Code-Qualität

```bash
# Alles linten
.devtools/lint.sh

# PHP Code Style (CS Fixer)
.devtools/php-cs-fixer.sh

# TypeScript Typprüfung
.devtools/yarn.sh tsc:check

# Stylelint
.devtools/yarn.sh lint:fix

# Prettier
.devtools/yarn.sh prettier:fix
```

### Vollständige CI/CD-Pipeline (Lokal)

```bash
# Alle Checks ausführen (wie GitHub Actions)
.devtools/pipeline.sh

# Bei Fehlern fortfahren
.devtools/pipeline.sh --continue-on-error

# Detaillierte Ausgabe
.devtools/pipeline.sh --verbose
```

Die Pipeline führt aus:
- Composer validate
- PHP CS Fixer
- PHPStan (Statische Analyse)
- PHPUnit-Tests
- Twig-Linting
- TypeScript-Checks
- Stylelint
- Asset-Build
- Prettier

---

## Deployment

### Produktions-Deployment

```bash
# Vom Projekt-Root (nicht .devtools!)
./deploy.sh
```

Das Deploy-Script:
1. Installiert Composer-Dependencies (--no-dev)
2. Installiert Yarn-Dependencies
3. Baut Produktions-Assets
4. Führt Datenbank-Migrations aus
5. Leert und wärmt Cache auf

### Migrations überspringen

```bash
SKIP_MIGRATIONS=true ./deploy.sh
```

### Manuelle Deployment-Schritte

```bash
composer install --no-dev --optimize-autoloader
yarn install --frozen-lockfile
yarn encore production
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

---

## Dokumentation

- **DevTools:** `.devtools/readme.md`
- **Entwicklung:** `docs/development-local.md`, `docs/development-docker.md`
- **Scripts:** `docs/scripts.md`
- **Datenbank:** `docs/database.md`
- **Geheimnisse:** `docs/secrets.md`

---

## CI/CD

[![PHP CI](https://github.com/bassix/unisurf/actions/workflows/php.yml/badge.svg?branch=main)](https://github.com/bassix/unisurf/actions/workflows/php.yml)
[![Node CI](https://github.com/bassix/unisurf/actions/workflows/node.yml/badge.svg?branch=main)](https://github.com/bassix/unisurf/actions/workflows/node.yml)

GitHub Actions Workflows:
- **PHP CI:** Composer, CS Fixer, PHPStan, PHPUnit, Twig-Lint
- **Node CI:** TypeScript, Stylelint, Webpack-Build, Prettier

---

## Mitwirken

1. Repository forken
2. Feature-Branch erstellen
3. `.devtools/pipeline.sh` vor dem Commit ausführen
4. Pull Request erstellen

---

## Lizenz

MIT – siehe [license](license)

---

## Kontakt

Fragen oder Projektanfragen: https://unisurf.de/kontakt
