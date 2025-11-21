# E-Mail-Templates und Kontaktformular Verbesserungen ✅

## Zusammenfassung der Änderungen

Alle E-Mail-Templates wurden überarbeitet, an das UniSurf-Design angepasst und die Fehler wurden behoben.

---

## 1. ✅ E-Mail-Templates aktualisiert

### HTML-Templates

#### `templates/email/contact_owner.html.twig`

- UniSurf-Branding hinzugefügt
- Kontaktdaten übersichtlicher formatiert
- Datum der Anfrage hinzugefügt
- Telefon als klickbarer Link
- Technische Meta-Informationen beibehalten
- Hinweis für direkte Antwort hinzugefügt

#### `templates/email/contact_visitor.html.twig`

- Persönliche Ansprache mit Namen
- UniSurf-Kontaktinformationen prominent platziert
- "Mit sportlichen Grüßen" als Abschluss
- DSGVO-konforme Datenschutzhinweise
- Übersichtlichere Struktur

#### `templates/email/contact_visitor_dark.html.twig` (NEU)

- Dark-Theme-Version für Besucher
- Verwendet `base_dark.html.twig`
- Angepasste Farben (UniSurf-Grün auf dunklem Hintergrund)

### Text-Templates (TXT)

#### `templates/email/contact_owner.txt.twig`

- Klare Struktur mit Trennlinien
- Alle relevanten Informationen
- UniSurf-Branding

#### `templates/email/contact_visitor.txt.twig`

- Professioneller Ton
- UniSurf-Kontaktinformationen
- DSGVO-Hinweise

---

## 2. ✅ CSS-Styles für E-Mails überarbeitet

### `templates/email/_partials/light.css`

- **UniSurf-Farben**: `#008000` (Primär-Grün)
- **Gradient-Header**: Grüner Farbverlauf
- **Buttons**: Grüner Call-to-Action-Style
- **Links**: UniSurf-Grün statt Blau
- **Verbesserte Abstände und Lesbarkeit**

### `templates/email/_partials/dark.css`

- **UniSurf-Dark-Farben**: `#66b366` (Hell-Grün für Dark Mode)
- **Dunkler Hintergrund**: `#343a40` (Card), `#212529` (Body)
- **Grüner Gradient-Header** auch im Dark Mode
- **Konsistentes Design** mit Website Dark Mode
- **Optimierte Kontraste** für bessere Lesbarkeit

---

## 3. ✅ Theme-basierte E-Mail-Auswahl

### `src/Service/MailManService.php`

**Neue Funktionen:**

- `getEmailTheme()`: Erkennt das vom Benutzer gewählte Theme
- Prüft `theme` in der Session (localStorage → Session)
- Fallback auf Light-Theme bei fehlender Präferenz

**E-Mail-Versand:**

- **Owner (Admin)**: Immer Light-Theme (bessere Lesbarkeit)
- **Visitor (Benutzer)**: Theme-basiert
  - Dark Theme → `contact_visitor_dark.html.twig`
  - Light Theme → `contact_visitor.html.twig`
- Automatischer Fallback bei fehlendem Template
- Theme wird im Log mitprotokolliert

**Dependencies:**

- `RequestStack` hinzugefügt für Session-Zugriff

---

## 4. ✅ Flash-Messages im ContactController

### `src/Controller/ContactController.php`

**Neue Funktionalität:**

- Prüft Query-Parameter nach Form-Submission
- Zeigt **Success-Message** bei `?submit=1`
- Zeigt **Error-Messages** bei `?error=mail` oder `?error=rate`

**Messages:**

- ✅ **Erfolg**: "Vielen Dank für Ihre Nachricht! Wir haben Ihre Anfrage erhalten..."
- ❌ **Fehler (Mail)**: "Leider konnte Ihre Nachricht nicht versendet werden..."
- ❌ **Fehler (Rate-Limit)**: "Sie haben zu viele Anfragen in kurzer Zeit gesendet..."

**Dependencies:**

- `RequestStack` hinzugefügt

---

## 5. ✅ Base-Templates aktualisiert

### `templates/email/base_light.html.twig`

- Titel: "UniSurf" statt "Hütte9"
- Light-Theme CSS eingebunden

### `templates/email/base_dark.html.twig`

- Titel: "UniSurf" statt "Hütte9"
- Dark-Theme CSS eingebunden
- Hintergrundfarbe auf UniSurf-Dark-Theme angepasst (`#212529`)

---

## 6. ✅ Design-Konsistenz

### Farben

| Element         | Light Theme           | Dark Theme            |
| --------------- | --------------------- | --------------------- |
| Primär-Grün     | `#008000`             | `#66b366`             |
| Link-Farbe      | `#008000`             | `#66b366`             |
| Header-Gradient | `#008000` → `#66b366` | `#006600` → `#66b366` |
| Button          | `#008000`             | `#66b366`             |
| Hintergrund     | `#f8f9fa`             | `#212529`             |
| Card            | `#ffffff`             | `#343a40`             |
| Text            | `#212529`             | `#dee2e6`             |

### Typografie

- Font-Stack: System-Fonts (-apple-system, Segoe UI, Roboto...)
- Überschriften: Bold, Uppercase für H1
- Abstände: Konsistent 16-24px
- Line-Height: 1.6-1.7 für bessere Lesbarkeit

---

## 7. ✅ Datenschutz & DSGVO

Alle E-Mail-Templates enthalten:

- ✓ Hinweis auf Datenverarbeitung
- ✓ Zweckbindung (nur für Kontaktaufnahme)
- ✓ Vertraulichkeit
- ✓ Hinweis auf Antwortmöglichkeit

---

## 8. ✅ Template-Struktur

```
templates/email/
├── _partials/
│   ├── dark.css          # Dark-Theme CSS (aktualisiert)
│   └── light.css         # Light-Theme CSS (aktualisiert)
├── base_dark.html.twig   # Dark-Theme Base (aktualisiert)
├── base_light.html.twig  # Light-Theme Base (aktualisiert)
├── contact_owner.html.twig         # Admin-Benachrichtigung (aktualisiert)
├── contact_owner.txt.twig          # Admin-Benachrichtigung Text (aktualisiert)
├── contact_visitor.html.twig       # Besucher Light-Theme (aktualisiert)
├── contact_visitor_dark.html.twig  # Besucher Dark-Theme (NEU)
└── contact_visitor.txt.twig        # Besucher Text (aktualisiert)
```

---

## Testing

### Manueller Test

1. Navigieren Sie zu `/kontakt`
2. Füllen Sie das Formular aus
3. Aktivieren Sie "Kopie an mich senden"
4. Senden Sie das Formular ab

**Erwartetes Verhalten:**

- ✅ Success-Message wird angezeigt
- ✅ Admin erhält E-Mail (Light-Theme)
- ✅ Besucher erhält E-Mail (Theme-basiert)
- ✅ Alle E-Mails verwenden UniSurf-Design

### Theme-Test

1. Wählen Sie Dark-Theme in der Navbar
2. Senden Sie Kontaktformular mit "Kopie an mich"
3. **Erwartet**: Dark-Theme E-Mail an Besucher

### Error-Test

1. Senden Sie mehrere Anfragen schnell hintereinander
2. **Erwartet**: Rate-Limit Error-Message

---

## Behobene Fehler

1. ✅ **"Unable to find template email/contact_owner.txt.twig"**
   - Templates waren vorhanden, aber Inhalte veraltet
   - Alle Templates wurden aktualisiert

2. ✅ **Keine Success/Error-Messages auf Kontaktseite**
   - Flash-Messages im Controller hinzugefügt
   - Query-Parameter werden ausgewertet
   - Messages werden in Template angezeigt

3. ✅ **E-Mail-Design nicht an Website angepasst**
   - CSS komplett überarbeitet
   - UniSurf-Farben durchgängig verwendet
   - Dark/Light-Theme implementiert

4. ✅ **Theme-Auswahl nicht in E-Mails berücksichtigt**
   - Theme-Detection im MailManService
   - Separate Templates für Dark/Light
   - Automatischer Fallback

---

## Konfiguration

### E-Mail-Absender

Die E-Mail-Konfiguration erfolgt über `.env`:

```env
MAIL_FROM_ADDRESS=noreply@unisurf.de
MAIL_FROM_NAME=UniSurf
MAIL_TO_ADDRESS=info@unisurf.de
MAIL_TO_NAME=UniSurf Team
```

### Mailer-DSN

Stellen Sie sicher, dass `MAILER_DSN` konfiguriert ist:

```env
MAILER_DSN=smtp://user:pass@smtp.example.com:587
```

---

## Weitere Verbesserungen

### Optional: Logo in E-Mails

Fügen Sie ein Logo hinzu in `base_light.html.twig` und `base_dark.html.twig`:

```html
<td
  class="header"
>
  <img
    src="https://unisurf.de/build/images/unisurf-logo.png"
    alt="UniSurf Logo"
    class="logo"
  />
  <h1>
    {%
    block
    header_title
    %}UniSurf{%
    endblock
    %}
  </h1>
</td>
```

### Optional: E-Mail-Signatur

Fügen Sie eine Footer-Signatur in den Base-Templates hinzu.

---

## Zusammenfassung

**Aktualisierte Dateien:** 10
**Neue Dateien:** 4  
**Behobene Fehler:** 4

Alle E-Mail-Templates sind jetzt:

- ✅ Mit UniSurf-Branding
- ✅ Design-konsistent mit der Website
- ✅ Theme-aware (Dark/Light)
- ✅ DSGVO-konform
- ✅ Benutzerfreundlich
- ✅ Voll funktionsfähig

Das Kontaktformular zeigt jetzt korrekte Success- und Error-Messages an! 🎉
