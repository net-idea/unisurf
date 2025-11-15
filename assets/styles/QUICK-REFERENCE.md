# SCSS Quick Reference Guide

## File Structure

```
assets/styles/
├── fonts.scss           # Font @font-face declarations
├── app.scss            # Main entry (imports only)
├── _variables.scss     # All configurable variables ⚙️
├── _base.scss          # Global base styles
├── _theme.scss         # Theme components (nav, masthead, footer)
├── _forms.scss         # Form styles
├── _theme-light.scss   # Light theme overrides
└── _theme-dark.scss    # Dark theme overrides
```

## Quick Edit Guide

### Change Colors

**File:** `_variables.scss`

```scss
$unisurf-green-primary: #008000; // Main brand color
$unisurf-green-light: #66b366; // Light variant
```

### Change Typography

**File:** `_variables.scss`

```scss
$font-size-h1: 2.5rem;
$font-size-h2: 1.6rem;
$font-size-base: 1.26rem;
```

### Change Navigation

**File:** `_theme.scss` (lines 24-103)

```scss
#mainNav {
  // Edit navigation styles
}
```

### Change Forms

**File:** `_forms.scss` (base styles)
**File:** `_theme-light.scss` (light theme)
**File:** `_theme-dark.scss` (dark theme)

### Add Dark Theme Style

**File:** `_theme-dark.scss`

```scss
[data-bs-theme='dark'] {
  .your-component {
    background-color: #343a40;
    color: #dee2e6;
  }
}
```

## Variable Quick Reference

### Colors

```scss
$unisurf-green-primary: #008000;
$unisurf-green-light: #66b366;
$unisurf-green-dark: #006600;
$primary: $unisurf-green-primary;
```

### Fonts

```scss
$font-family-primary:
  'Montserrat',
  system-ui,
  -apple-system,
  sans-serif;
$font-family-secondary:
  'Roboto Slab',
  Georgia,
  serif;
```

### Spacing

```scss
$section-padding: 6rem
  0;
$heading-margin-bottom: 1rem;
```

### Navigation

```scss
$nav-padding-y: 1rem;
$nav-logo-height: 2.5rem;
$nav-transition-speed: 0.3s;
```

## Build Commands

```bash
# Development with watch
npm run watch

# Development build
npm run dev

# Production build
npm run build
```

## Import Order in app.scss

1. Bootstrap functions
2. UniSurf variables
3. Bootstrap variables
4. Bootstrap components
5. UniSurf base styles
6. UniSurf theme
7. UniSurf forms
8. Theme overrides (light/dark)

## Common Tasks

### Add New Variable

1. Open `_variables.scss`
2. Add variable in appropriate section
3. Use variable in component files

### Modify Component

1. Find component in `_theme.scss` or `_base.scss`
2. Edit styles
3. Run `npm run build`

### Add Custom Utility Class

**File:** `_base.scss` (bottom)

```scss
.my-utility {
  color: $primary;
}
```

### Override Bootstrap Component

**File:** After Bootstrap imports in `app.scss` or in `_base.scss`

```scss
.btn {
  border-radius: 0.5rem;
}
```

## Tips

✅ **Always edit `_variables.scss` first** - Don't hardcode values
✅ **Use partials (`_*.scss`)** - They won't compile individually
✅ **Test both themes** - Check light and dark modes
✅ **Run build after changes** - Verify no errors
✅ **Use existing variables** - Don't create duplicates

## File Line Counts

```
fonts.scss        300 lines
app.scss           95 lines
_variables.scss   109 lines
_base.scss         82 lines
_theme.scss       165 lines
_forms.scss        48 lines
_theme-light.scss  30 lines
_theme-dark.scss   60 lines
```

---

**Need more details?** See `REFACTORING-COMPLETE.md`
