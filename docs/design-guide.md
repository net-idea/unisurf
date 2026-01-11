# UniSurf – Design & Pages Guide

This document describes the **actual** page structure and UI building blocks of this project.

It is meant for:

- Designers/Content editors: where texts live and how pages are structured
- Developers: where to change layout, components, and styling

## Where things live

### Page templates

- `templates/base.html.twig`: global HTML layout, SEO meta generation, shared masthead
- `templates/_partials/navbar.html.twig`: navigation + theme dropdown
- `templates/_partials/footer.html.twig`: footer
- `templates/pages/*.html.twig`: individual marketing pages
  - `index.html.twig`
  - `services.html.twig`
  - `hosting.html.twig`
  - `entwicklung.html.twig`
  - `kontakt.html.twig`
- `templates/pages/content.html.twig`: generic template for CMS/Markdown pages (Impressum, Datenschutz)

### Alternative contents (CMS pages)

- `content/_pages.php`: central metadata (title/description/keywords/canonical/nav items)
- `content/*.md`: Markdown pages (e.g. `impressum.md`, `datenschutz.md`)

### Styles

Entry:

- `assets/styles/app.scss` imports Bootstrap + UniSurf modules.

Key modules:

- `assets/styles/_variables.scss`: design tokens (colors, spacing, font sizes)
- `assets/styles/_theme.scss`: shared components (masthead, nav, footer, cards, typography helpers)
- `assets/styles/_theme-dark.scss` / `_theme-light.scss`: overrides per theme
- `assets/styles/_forms*.scss`: form styling + dark/light variants

## Global UI / Layout

### Base layout (`templates/base.html.twig`)

- Language: `de`
- SEO:
  - canonical URL computed from slug
  - `pageMeta.*` values used when available
  - OpenGraph + Twitter card tags
- Assets: Webpack Encore entry `app` is loaded via `encore_entry_link_tags('app')` and `encore_entry_script_tags('app')`.

### Navigation + theme switcher (`templates/_partials/navbar.html.twig`)

- Sticky/fixed top navigation (`#main-nav`)
- `navItems` are provided by the backend (based on `content/_pages.php`)
- Theme switcher dropdown:
  - `data-theme="light"|"dark"|"system"`
  - styling is handled in `assets/styles/_theme*.scss`

### Masthead (hero)

All major pages use the `.masthead` header.

- Background image is set inline in Twig (currently: `images/header-bg.jpg`)
- Overlay and typography are styled in `assets/styles/_theme.scss` under `header.masthead`

## Pages (routes + sections)

> Note: Routes are German slugs (e.g. `/kontakt`). Page meta/navigation config is in `content/_pages.php`.

### Home (`/`) – `templates/pages/index.html.twig`

Hero (`masthead` block):

- Subheading: "Ihr Partner für digitale Infrastruktur und Managed Hosting"
- Heading: "Verlässlich. Persönlich. Kompetent."
- CTA buttons:
  - primary → `/kontakt`
  - secondary → `/services`

Body sections:

1. **Überblick / Value Proposition** (`#home-overview`)
   - "Unser Angebot" kicker + H1
   - feature list `.unisurf-feature-list` with check icons
   - two feature cards `.page-section-card` (Managed Hosting / Infrastruktur & Vernetzung)
   - right-side image (`images/webhosting.webp`) inside `.media-block`

2. **Kernleistung: Managed Hosting & Infrastruktur** (`#hosting`, background `bg-body-tertiary`)
   - bullet list of hosting benefits
   - supporting cards (`Unser Fokus`, `Wartung & Pflege`, `Skalierung`)

3. **Zusatzleistungen / Leistungsübersicht** (`#extras`)
   - grid of `.page-section-card` (Server & Cloud, Kommunikation & Daten, Vernetzung, IT-Sicherheit)

4. **Zielgruppe & Vertrauen** (`#target`, background `bg-body-tertiary`)
   - target groups list
   - advantages cards (Direkter Draht, Transparenz, Nachhaltigkeit, Flexibilität)

5. **Final CTA** (`#cta`)
   - centered card with CTA to `/kontakt` and `/services`

### Services (`/services`) – `templates/pages/services.html.twig`

- Intro section with kicker "Services" + H1
- Main grid section (`#services`) containing service cards (`.page-section-card`):
  - Managed Server & Administration
  - Webhosting & Web-Umgebungen
  - Vernetzung Haus Intern
  - Datenbanken & Speicher
  - Cloud-Integration & Beratung
  - KI & Automatisierung
  - IT-Sicherheit
- Final consultation CTA card ("Technologie mit Augenmaß")

### Hosting (`/hosting`) – `templates/pages/hosting.html.twig`

- Intro section with kicker "Hosting" + H1
- Content section with:
  - target groups list
  - large media block image (`images/webhosting-01.png`)
  - feature cards (Managed Server, Webhosting, E-Mail Service, Datenbank-Management)
- Closing quality CTA card

### Entwicklung (`/entwicklung`) – `templates/pages/entwicklung.html.twig`

- Intro section with kicker "Entwicklung" + H1
- Sections implemented as stacked `.page-section-card` blocks:
  - Webseiten & Unternehmensportale
  - E-Commerce Lösungen
  - Interne Vernetzung & Intranet
  - Schnittstellen & Web-Applikationen
  - Arbeitsweise (list)
- Closing CTA cards:
  - "Entwicklung und Betrieb aus einer Hand" → `/hosting`
  - "Wir ergänzen Ihre Möglichkeiten" → `/kontakt`

### Kontakt (`/kontakt`) – `templates/pages/kontakt.html.twig`

Hero (masthead block):

- Subheading: "Kontakt"
- Heading: "Wir sind für Sie da"

Body (`#contact`):

- Intro text "Nachricht senden"
- Flash-/Query-parameter alert boxes (`#contact-success`, `#contact-error`, `#contact-success-redirect`, `#contact-error-redirect`)
- Symfony Form rendering with Bootstrap form classes:
  - name, email, phone, message
  - consent checkbox
  - honeypot fields: `website`, `emailrep`
- Additional Datenschutz info card linking to `/datenschutz`

Styling hints:

- Form styles live in `assets/styles/_forms.scss` + theme overrides
- Bootstrap validation classes (`is-invalid`, `.invalid-feedback`) are used

### CMS / Markdown pages (`/impressum`, `/datenschutz`)

- Content is stored in `content/impressum.md` and `content/datenschutz.md`
- Rendered via `templates/pages/content.html.twig`
- Metadata is configured in `content/_pages.php` (`cms => true`)

## Design system notes

### Reusable building blocks

- `.page-section-card`: main "card" container for content blocks
- `.section-kicker`: small eyebrow heading used above titles
- `.media-block`: image container used on marketing pages
- `.btn-xl`: larger CTA button

### Themes

Theme switching is implemented via the navbar dropdown.

Relevant SCSS files:

- `assets/styles/_theme.scss` (base styling)
- `assets/styles/_theme-dark.scss`
- `assets/styles/_theme-light.scss`

## How to change the site

- Update copy/text:
  - for marketing pages: edit `templates/pages/*.html.twig`
  - for legal pages: edit `content/*.md`
- Add/remove navigation entries:
  - edit `content/_pages.php` (`nav`, `nav_label`, `nav_order`)
- Change colors/spacing:
  - start in `assets/styles/_variables.scss`
- Change masthead overlay or typography:
  - edit `assets/styles/_theme.scss` → `header.masthead`

## Quick sanity check

When you change templates or SCSS you can validate quickly:

- Local mode: `./develop.sh` → option 1
- Docker mode: `./develop.sh` → option 2

Then open:

- `/` (home)
- `/services`
- `/hosting`
- `/entwicklung`
- `/kontakt`
- `/impressum`
- `/datenschutz`
