# GitHub Copilot Instructions for UniSurf

You are an expert AI programming assistant working on the UniSurf project.
Follow these project-specific instructions to ensure code quality, consistency, and accuracy ("Zero Hallucination").

## 📋 Project Tech Stack

- **Framework**: Symfony 8 (PHP 8.3+)
- **Frontend**: Webpack Encore, Bootstrap 5, Stimulus, TypeScript
- **Database**: MariaDB / MySQL / SQLite / PostgreSQL (Doctrine ORM)
- **Testing**: PHPUnit, PHPStan (Level 4+)

## 🛑 Coding Guidelines & Constraints (Strict)

### 1. PHP / Backend Rules

- **Strict Logic**: Always add `declare(strict_types=1);` at the top of every PHP file.
- **Attributes ONLY**: Use PHP 8 Attributes for all metadata mapping.
  - ✅ `#[Route('/path', name: 'app_home')]`
  - ❌ `/** @Route("/path", name="app_home") */`
  - ✅ `#[ORM\Entity]`
  - ❌ `/** @ORM\Entity */`
- **Modern PHP**: Use Constructor Property Promotion, `readonly` execution classes, and `match` expressions where applicable.
- **Dependency Injection**: Always use Dependency Injection (constructor injection). Never use `$this->container->get()`.
- **Repository Pattern**: Do not write complex SQL or DQL in Controllers. Use Repository methods.

### 2. Frontend / JavaScript Rules

- **Stimulus Controllers**: For any dynamic behavior, create a Stimulus Controller in `assets/controllers/`.
  - ❌ Do not write inline `<script>` tags in Twig templates.
  - ❌ Do not use jQuery or unrelated vanilla JavaScript files.
- **Bootstrap 5 Utilities**: Prioritize Bootstrap utility classes (e.g., `p-3 mb-4 d-flex gap-2`).
  - Only write custom SCSS in `assets/styles/` if Bootstrap utilities cannot achieve the design.
- **TypeScript**: Use TypeScript (`.ts`) for all new frontend logic.

### 3. "Zero Hallucination" Policy

- **Verify Context**: Do not reference classes, services, or variables unless you are sure they exist in the current file context or have been explicitly provided.
- **No Ghost Dependencies**: Do not suggest installing new Composer or Yarn packages unless standard solutions are exhausted. Use what is in `composer.json` and `package.json`.
- **Environment Variables**: Never emit code with hardcoded credentials. Always assume usage of `$_ENV` or `$params->get()`.
- **URL Handling**: Use `path()` in Twig and `$urlGenerator->generate()` in PHP. Never hardcode URLs like `/contact`.

## 📂 Key Directories Reference

- `assets/styles/` - SCSS Stylesheets
- `assets/scripts/` - TypeScript Scripts
- `assets/controllers/` - Stimulus Controllers (Frontend Logic)
- `src/Command/` - CLI Commands
- `src/Controller/` - HTTP Request Handlers
- `src/Entity/` - Doctrine Entities (Database Schema)
- `src/Form/` - Symfony Forms
- `src/Repository/` - Doctrine Repositories
- `src/Services/` - Services with the main business logic
- `migrations/` - Database Migrations
- `templates/` - Twig Views
- `test/` - PHPUnit Tests for the application and website
- `translations/` - Translation Strings

## 🧪 Testing Guidelines

- Write tests in `tests/`.
- Extend `Symfony\Bundle\FrameworkBundle\Test\WebTestCase` for functional tests.
- Extend `PHPUnit\Framework\TestCase` for unit tests.

## 📝 Common Tasks

- **Creating a Migration**: `php bin/console make:migration`
- **Running Migrations**: `php bin/console doctrine:migrations:migrate`
- **Building Assets**: `yarn watch` (dev) or `yarn build` (prod)
