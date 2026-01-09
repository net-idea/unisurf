#!/bin/bash

# Initialize database locally or via Docker Compose
# Usage: ./database-init.sh [--engine mariadb|postgres] [--local]

set -euo pipefail

if [ -f .env ]; then
  set -o allexport
  source .env
  set +o allexport
fi

APP_NAME="${APP_NAME:-unisurf}"
PROJECT_NAME="$APP_NAME"
ENGINE="${DB_ENGINE:-mariadb}" # mariadb or postgres
MODE="compose" # or local

while [[ $# -gt 0 ]]; do
  case "$1" in
    --engine)
      ENGINE="$2"; shift 2 ;;
    --local)
      MODE="local"; shift ;;
    -h|--help)
      echo "Usage: $0 [--engine mariadb|postgres] [--local]"; exit 0 ;;
    *)
      echo "Unknown option: $1" >&2; exit 1 ;;
  esac
done

run_php() {
  if [ "$MODE" = "local" ]; then
    php "$@"
  else
    docker compose -p "$PROJECT_NAME" "${COMPOSE_FILES[@]}" exec php php "$@"
  fi
}

if [ "$MODE" = "local" ]; then
  echo "Using local PHP/DB (DATABASE_URL-driven)."
else
  COMPOSE_FILES=(
    -f docker-compose.yaml
    -f docker-compose.dev.yaml
  )
  if [ "$ENGINE" = "postgres" ]; then
    COMPOSE_FILES+=( -f docker-compose.postgresql.yml -f docker-compose.postgresql.dev.yml )
    DB_SERVICE=postgres
  else
    COMPOSE_FILES+=( -f docker-compose.mariadb.yml -f docker-compose.mariadb.dev.yml )
    DB_SERVICE=database
  fi
  COMPOSE_FILES+=( -f docker-compose.adminer.yml -f docker-compose.phpmyadmin.yml )

  echo "Ensuring DB service is up ($ENGINE)..."
  docker compose -p "$PROJECT_NAME" "${COMPOSE_FILES[@]}" up -d --build --force-recreate
  # quick readiness loop
  for i in {1..60}; do
    state=$(docker compose -p "$PROJECT_NAME" "${COMPOSE_FILES[@]}" ps "$DB_SERVICE" --format json 2>/dev/null | grep -o '"State":"[^\"]*"' | cut -d'"' -f4)
    if [ "$state" = "running" ]; then
      break
    fi
    sleep 1
  done
fi

echo "Running migrations..."
run_php bin/console doctrine:migrations:migrate --no-interaction

echo "✅ Database ready (engine=$ENGINE, mode=$MODE)."
