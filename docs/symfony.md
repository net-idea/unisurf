# Symfony commands and troubleshooting

This document lists common Symfony commands used in this project and troubleshooting tips.

## Common commands (local / host)

Clear the application cache (local):

```bash
php bin/console cache:clear
```

Create a new migration skeleton:

```bash
php bin/console make:migration
```

Run database migrations:

```bash
php bin/console doctrine:migrations:migrate
```

Validate the Doctrine schema against entities:

```bash
php bin/console doctrine:schema:validate
```

Run the test suite (local):

```bash
# Run tests
php bin/phpunit
```

## Common commands (Docker)

Prefer the helper script `php.sh` (it targets the `php` service of the current compose project):

Clear the application cache (inside Docker):

```bash
# Clear cache
./php.sh bin/console cache:clear
```

Create a new migration skeleton (inside Docker):

```bash
./php.sh bin/console make:migration
```

Run database migrations non-interactively (inside Docker):

```bash
./php.sh bin/console doctrine:migrations:migrate --no-interaction
```

Validate the Doctrine schema (inside Docker):

```bash
./php.sh bin/console doctrine:schema:validate
```

Run the test suite (inside Docker):

```bash
./php.sh bin/phpunit
```

## Project-specific commands

These are custom console commands implemented in this project. Use the local PHP binary or the `./php.sh` helper inside Docker as shown above.

Show a list of stored contact submissions:

```bash
php bin/console app:list:contacts
```

Preview the contact notification email in the console (custom):

```bash
php bin/console app:mail:preview-contact
```

Generate or regenerate the application secret (writes to .env.local or .env):

```bash
php bin/console app:secret
```

Docker: Show a list of stored contact submissions:

```bash
./php.sh bin/console app:list:contacts
```

Docker: Preview the contact notification email:

```bash
./php.sh bin/console app:mail:preview-contact
```

Docker: Generate/regenerate the application secret:

```bash
./php.sh bin/console app:secret
```

## Troubleshooting

### Cache issues

- Clear cache: `php bin/console cache:clear`
- In prod mode: `php bin/console cache:clear --env=prod --no-debug`

### .env configuration

- Use `.env.local` for local overrides
- Ensure `APP_ENV`, `DATABASE_URL`, and `APP_SECRET` are set correctly

### Asset build issues

Local:

- Reinstall node modules: `yarn install`
- Rebuild: `yarn encore dev` / `yarn encore dev --watch` / `yarn encore production`

Docker:

- Use `./docker-yarn.sh …`

## Related docs

- Docker: `docs/development-docker.md`
- Local dev: `docs/development-local.md`
- Database: `docs/database.md`
