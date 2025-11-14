/*
 * Welcome to your app's main TypeScript entry file!
 *
 * We recommend including the built version of this file
 * (and its CSS) in your base layout (base.html.twig).
 */

// Bootstrap CSS (from node_modules – local)
import 'bootstrap/dist/css/bootstrap.min.css';

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// Shared theme styles
import './styles/theme.css';

// Light and Dark theme styles
import './styles/theme-dark.css';
import './styles/theme-light.css';

// Form styles
import './styles/form.css';

// Light and Dark form styles
import './styles/form-dark.css';
import './styles/form-light.css';

// Font Awesome (local)
import '@fortawesome/fontawesome-free/css/all.min.css';

// Import Bootstrap JavaScript (local bundle with Popper)
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Start the Stimulus application (TypeScript)
import './bootstrap';

// The main TypeScript file for project
import './scripts/main';

// Import TypeScript
import './scripts/theme-toggle';
