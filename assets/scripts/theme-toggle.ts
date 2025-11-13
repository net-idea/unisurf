const STORAGE_KEY = 'theme';
const THEME_LIGHT = 'light';
const THEME_DARK = 'dark';

type Theme = 'light' | 'dark' | 'system';

// Initialize theme ASAP to avoid flash
(function initializeTheme() {
  try {
    const stored = localStorage.getItem(STORAGE_KEY);
    const mql = window.matchMedia('(prefers-color-scheme: dark)');
    const theme = (stored === THEME_LIGHT || stored === THEME_DARK)
      ? stored
      : (mql.matches ? THEME_DARK : THEME_LIGHT);
    document.documentElement.setAttribute('data-bs-theme', theme);
  } catch (e) {
    // Ignore localStorage errors
  }
})();

// Theme toggler: reads from dropdown items with [data-theme]
(function attachThemeToggle() {
  function applyTheme(theme: Theme): void {
    if (theme === 'system') {
      localStorage.removeItem(STORAGE_KEY);
      const mql = window.matchMedia('(prefers-color-scheme: dark)');
      document.documentElement.setAttribute('data-bs-theme', mql.matches ? THEME_DARK : THEME_LIGHT);
    } else if (theme === THEME_LIGHT || theme === THEME_DARK) {
      localStorage.setItem(STORAGE_KEY, theme);
      document.documentElement.setAttribute('data-bs-theme', theme);
    }
  }

  // Handle system preference changes if no explicit theme set
  try {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (!stored) {
      const mql = window.matchMedia('(prefers-color-scheme: dark)');
      mql.addEventListener('change', (e) => {
        if (!localStorage.getItem(STORAGE_KEY)) {
          document.documentElement.setAttribute('data-bs-theme', e.matches ? THEME_DARK : THEME_LIGHT);
        }
      });
    }
  } catch (e) {
    // Ignore errors
  }

  document.addEventListener('click', (e: MouseEvent) => {
    if (!e.target) return;
    const btn = (e.target as HTMLElement).closest('[data-theme]');
    if (btn) {
      const theme = btn.getAttribute('data-theme') as Theme;
      if (theme) {
        applyTheme(theme);
      }
    }
  });
})();
