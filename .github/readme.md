# GitHub Actions Workflows

This project uses GitHub Actions for Continuous Integration (CI). There are two separate workflow files:

## 📋 Workflows

### 1. `php.yml` - PHP Quality Checks

Runs all PHP-related tests and quality checks:

- ✅ Composer Validation
- ✅ PHP CS Fixer (Code Style)
- ✅ PHPStan (Static Analysis)
- ✅ PHPUnit (Unit Tests)
- ✅ Twig Template Linting

**Matrix:** PHP 8.3 and 8.4

### 2. `node.yml` - Node/Yarn Quality Checks

Runs all Node/Yarn-related tests and quality checks:

- ✅ TypeScript Type Checking
- ✅ CSS/SCSS Linting (Stylelint)
- ✅ Asset Build (Webpack Encore)
- ✅ Prettier Code Formatting

**Matrix:** Node 20.x and 22.x

## 🚀 Trigger

All workflows are triggered on:

- Push to `main` or `develop` branch
- Pull Requests against `main` or `develop` branch

## 🔍 Local Development

### 🚀 Pipeline Script (Recommended!)

The **easiest** and **fastest** way to run all checks locally:

```bash
# Run all CI/CD checks at once
./pipeline.sh

# With detailed output
./pipeline.sh --verbose

# Run all checks, even on errors
./pipeline.sh --continue-on-error

# Show help
./pipeline.sh --help
```

The `pipeline.sh` script runs **exactly the same checks** as GitHub Actions:

- ✅ PHP Quality Checks (6 Steps)
- ✅ Node/Yarn Quality Checks (4 Steps)
- ✅ Code Formatting Check (1 Step)

**Output:**

```
=====================================
UniSurf CI/CD Pipeline
=====================================
PHP Version: 8.3.14
Node Version: v22.11.0

=====================================
PHP Quality Checks
=====================================
▶ Validate composer.json and composer.lock
✓ Validate composer.json and composer.lock - OK
...

=====================================
Pipeline Summary
=====================================
✓ All checks passed! ✨
Duration: 45s

Your code is ready to be pushed! 🚀
```

### Individual Checks

#### Run PHP checks locally:

```bash
# Validate Composer
composer validate --strict

# PHP CS Fixer
php ./vendor/bin/php-cs-fixer check -n --config=.php-cs-fixer.dist.php

# PHP CS Fixer (fix)
./php-cs-fixer.sh

# PHPStan
php ./vendor/bin/phpstan analyze src tests

# PHPUnit
php ./vendor/bin/phpunit tests

# Twig Linting
php bin/console lint:twig templates
```

#### Run Node checks locally:

```bash
# TypeScript Type Check
yarn run tsc:check

# CSS/SCSS Linting
yarn run lint:css

# CSS/SCSS Linting (fix)
yarn run lint:css:fix

# Asset Build
yarn run build

# Development Server
yarn run dev-server
```

#### Run Prettier locally:

```bash
# Check
npx prettier --check .

# Fix
npx prettier --write .
```

### Quick Overview:

```bash
./format.sh   # Format all files (Prettier + PHP CS Fixer)
./lint.sh     # Run all linting checks
./pipeline.sh # Run ALL CI/CD checks (recommended before push!)
```

## 📦 Caching

The workflows use caching for:

- **Composer:** `~/.composer/cache` (based on `composer.lock`)
- **Yarn:** Yarn Cache Directory (based on `yarn.lock`)

This significantly speeds up CI runs.

## ⚙️ Configuration Files

- **PHP CS Fixer:** `.php-cs-fixer.dist.php`
- **PHPStan:** `phpstan.dist.neon`
- **PHPUnit:** `phpunit.dist.xml`
- **TypeScript:** `tsconfig.json`
- **Stylelint:** `.stylelintrc.json`
- **Prettier:** `.prettierrc.json`
- **Prettier Ignore:** `.prettierignore`

## 🔍 Status Badges

You can add workflow status badges to your README:

```markdown
![PHP CI](https://github.com/YOUR_USERNAME/YOUR_REPO/workflows/PHP%20CI/badge.svg)
![Node CI](https://github.com/YOUR_USERNAME/YOUR_REPO/workflows/Node%20CI/badge.svg)
```

## 💡 Recommended Workflow

```bash
# 1. Development completed
# 2. Run all formatters
./format.sh

# 3. Test pipeline locally
./pipeline.sh

# 4. On success: Commit & Push
git add .
git commit -m "feat: new feature"
git push

# 5. GitHub Actions run automatically
```

## 🎯 Workflow Matrix

| Workflow | PHP 8.3 | PHP 8.4 | Node 20.x | Node 22.x |
| -------- | ------- | ------- | --------- | --------- |
| php.yml  | ✅      | ✅      | -         | -         |
| node.yml | -       | -       | ✅        | ✅        |

## 📚 Further Information

- See `~prompt/GITHUB_WORKFLOWS_SETUP.md` for detailed information about workflow setup
- See `~prompt/PIPELINE_SETUP.md` for information about the pipeline script
- See `~prompt/ci.yml` for backup of the old combined workflow file
