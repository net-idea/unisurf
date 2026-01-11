# Symfony commands and troubleshooting

This document lists common Symfony commands used in this project and troubleshooting tips.

## Common commands (local / host)

```bash
# Clear cache
php bin/console cache:clear

# Create a new migration
php bin/console make:migration

# Run migrations
php bin/console doctrine:migrations:migrate

# Validate database schema
php bin/console doctrine:schema:validate

# Run tests
php bin/phpunit
```

## Common commands (Docker)

Prefer the helper script `docker-php.sh` (it targets the `php` service of the current compose project):

```bash
# Clear cache
./php.sh bin/console cache:clear

# Create a new migration
./php.sh bin/console make:migration

# Run migrations
./php.sh bin/console doctrine:migrations:migrate --no-interaction

# Validate database schema
./php.sh bin/console doctrine:schema:validate

# Run tests
./php.sh bin/phpunit
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
