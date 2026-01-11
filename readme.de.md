# UniSurf

> Language: Deutsch · Englische Version: [readme.md](readme.md)

[![PHP CI](https://github.com/bassix/unisurf/actions/workflows/php.yml/badge.svg?branch=main)](https://github.com/bassix/unisurf/actions/workflows/php.yml)
[![Node CI](https://github.com/bassix/unisurf/actions/workflows/node.yml/badge.svg?branch=main)](https://github.com/bassix/unisurf/actions/workflows/node.yml)

Dieses Repository führt Continuous Integration via GitHub Actions aus. Die wichtigsten Workflows sind:

- PHP CI — composer validate, PHP CS Fixer, PHPStan (analysiert `src`), PHPUnit-Tests, Twig-Linting.
- Node CI — Installation, TypeScript-Typprüfung, Stylelint, Asset-Build (Webpack Encore), Prettier-Checks.

Die oben angezeigten Badges zeigen den Status für den `main`-Branch. Klicken Sie auf ein Badge, um vorige Workflow-Läufe und Logs anzusehen.

UniSurf steht für verlässliche, persönliche und kompetente digitale Infrastruktur: Managed Hosting, Webumgebungen und Entwicklung – aus einer Hand. Dieses Repository enthält die Website und das begleitende Management-System von UniSurf.

Kurz gesagt: Wir planen, betreiben und pflegen stabile Web‑Infrastruktur – von Managed Servern über Webhosting bis hin zu Datenbanken, E‑Mail‑Diensten und sicheren Schnittstellen. Unternehmen bekommen bei uns den direkten Draht, transparente Abläufe und Lösungen mit Augenmaß.

Live-Website: https://unisurf.de/

## Überblick für Besucherinnen und Besucher

Die Seite stellt unser Angebot verständlich vor und führt Sie gezielt zu den passenden Bereichen:

- Startseite (Überblick und Nutzenversprechen)
- Leistungen/Services (Managed Server & Administration, Webhosting, Vernetzung, Daten & Speicher, Cloud-Integration & Beratung, KI & Automatisierung, IT‑Sicherheit)
- Hosting (Zielgruppen, Leistungsumfang, Qualitätsversprechen)
- Entwicklung (Webseiten/Portale, E‑Commerce, interne Vernetzung, Schnittstellen & Web‑Applikationen, Arbeitsweise)
- Kontakt (Kontaktformular mit DSGVO‑Hinweisen)
- Rechtliches (Impressum, Datenschutz)

Alle Seiten sind responsiv, barrierearm und in hell/dunkel (Theme‑Switcher) nutzbar.

## Warum UniSurf?

- Direkter Draht: Persönlicher Kontakt statt Ticket-Wüste.
- Transparente Abläufe: Verständliche Prozesse, klare Verantwortlichkeiten.
- Stabilität & Wartbarkeit: Fokus auf zuverlässigen Betrieb und saubere Upgrades.
- Flexibilität: Von Managed Servern bis zu spezifischer Entwicklung – alles aus einer Hand.
- Sicherheit mit Augenmaß: Solide Standards, sinnvolle Absicherung und Datenschutz.

## Features

- Managed Hosting & Infrastruktur mit Fokus auf Stabilität und Wartbarkeit
- Webhosting & Web‑Umgebungen, E‑Mail‑Service, Datenbank‑Management
- Entwicklung von Webseiten, Portalen, Schnittstellen und internen Anwendungen
- Responsives Design auf Basis von Bootstrap 5, klare Typografie und wiederverwendbare Komponenten
- Hell/Dunkel‑Theme per Umschalter in der Navigation
- Kontaktformular mit Validierung, Spam‑Schutz (Honeypots) und Versand über Symfony Mailer
- Inhalte/Legal-Text als Markdown renderbar (Impressum, Datenschutz)
- Entwicklerfreundliche Umgebung mit Docker Compose, Adminer/phpMyAdmin und Mailpit

## Tech‑Stack

- Backend: Symfony 8 (PHP)
- Frontend: Twig, Webpack Encore (TypeScript/Sass), Bootstrap 5
- Datenbank: MariaDB (Standard) oder PostgreSQL
- Entwicklung: Docker Compose, Yarn, Helper‑Skripte

Weitere Details finden Sie in `composer.json` und `package.json`.

---

## Quick Start

Es gibt zwei empfohlene Entwicklungs‑Setups.

### 1) Lokal (Host‑Tools, DB via Docker)

```bash
cd unisurf.de
./develop.sh
# wählen: 1) Local (host tools) + MariaDB via Docker
```

App: `http://127.0.0.1:8000`

### 2) Komplett in Docker (Docker Compose)

```bash
cd unisurf.de
./develop.sh      # Attached (Logs im Vordergrund)
./develop.sh -d   # Detached (im Hintergrund)
# wählen: 2) Docker Compose (full dev stack)

# oder direkt:
./docker-start.sh
```

App: `http://localhost:8000`

### Dienste/Tools im Docker‑Setup

- App: `http://localhost:8000`
- Assets (Encore Dev‑Server): `http://localhost:8080`
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

### Datenbank Init / Migrationen

```bash
./database-init.sh           # startet DB (compose), wartet, führt Migrationen aus
./database-init.sh --reset   # drop volumes/data (compose), restart DB+php, Migrationen erneut
```

## Seiten & Links

- Live: https://unisurf.de/
- Lokale Routen während der Entwicklung (Docker/Local):
  - `/` (Startseite)
  - `/services`
  - `/hosting`
  - `/entwicklung`
  - `/kontakt`
  - `/impressum`
  - `/datenschutz`

## Dokumentation

Ausführliche Dokumentation liegt unter `docs/`:

- `docs/readme.md`
- Lokale Entwicklung: `docs/development-local.md`
- Docker‑Entwicklung: `docs/development-docker.md`
- Helper‑Skripte: `docs/scripts.md`
- Symfony‑Befehle: `docs/symfony.md`
- Datenbank: `docs/database.md`
- Sicherheit/Geheimnisse: `docs/secrets.md`

## Projektstruktur

Der Anwendungscode liegt im Ordner `unisurf.de/`. Seiten‑Templates befinden sich unter `templates/pages/*.html.twig`, Styles unter `assets/styles/`, rechtliche Inhalte unter `content/*.md`.

## Mitwirken (Contributing)

- Pull Requests willkommen. Bitte klare Commits und kurze Beschreibung.
- Code‑Style: Nutzen Sie die vorhandenen Lint/Format‑Skripte.
  - JavaScript/SCSS/Markup: `yarn lint` bzw. `yarn lint:fix`
  - PHP: `./php-cs-fixer.sh` (falls eingerichtet)
- Tests/Checks: Nutzen Sie bei Arbeiten im Docker‑Setup `./docker-test.sh` für einen schnellen Gesundheitscheck.

## Lizenz

MIT. Siehe `license`.

## Kontakt & Support

Fragen oder Projektanfragen? Wir freuen uns auf Ihre Nachricht:

- Kontaktformular: https://unisurf.de/kontakt

---

Hinweis zur Sicherheit: Bitte keine vertraulichen Zugangsdaten in Issues oder Pull Requests veröffentlichen. Nutzen Sie `.env.local` für lokale Variablen und beachten Sie `docs/secrets.md`.
