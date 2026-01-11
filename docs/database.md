# Database

This doc covers database usage and troubleshooting.

## Which database is used?

The project supports:

- **MariaDB** (default): compose service `database`
- **PostgreSQL**: compose service `postgres`

The selection is controlled via:

- `DB=mariadb|postgres` (used by the helper scripts)
- `DATABASE_URL=...` (used by Symfony/Doctrine)

Prefer setting overrides in `.env.local`.

## Migrations

Local:

```bash
php bin/console doctrine:migrations:migrate
```

Docker:

```bash
./php.sh bin/console doctrine:migrations:migrate --no-interaction
```

## Troubleshooting

### `SQLSTATE[HY000] [2002] No such file or directory` (MariaDB/MySQL)

This usually happens when your DSN uses `localhost` and PHP tries to connect via a unix socket.

Fix by using `127.0.0.1` (TCP) or the compose hostname `database` (inside Docker).

### Connection refused

- Check stack health:

```bash
./docker-test.sh
```

- Inspect which services/compose files are expected:

```bash
./docker-list.sh
```

- View DB logs:

```bash
docker compose -p "${APP_NAME:-unisurf}" logs -f database
```

### Validate schema

```bash
./php.sh bin/console doctrine:schema:validate
```
