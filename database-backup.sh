#!/bin/bash

set -euo pipefail

# Get environment variables
if [ -f .env ]; then
  set -o allexport
  source .env
  set +o allexport
fi

# Dynamic container naming based on APP_NAME from .env
APP_NAME="${APP_NAME:-unisurf}"
PROJECT_NAME="$APP_NAME"
ENGINE="${DB_ENGINE:-mariadb}" # mariadb or postgres
OUTPUT_DIR="backups"
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
FILE="${OUTPUT_DIR}/${PROJECT_NAME}-${ENGINE}-${TIMESTAMP}.sql"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --engine)
      ENGINE="$2"; shift 2 ;;
    --output-dir)
      OUTPUT_DIR="$2"; shift 2 ;;
    -h|--help)
      echo "Usage: $0 [--engine mariadb|postgres] [--output-dir DIR]"; exit 0 ;;
    *)
      echo "Unknown option: $1" >&2; exit 1 ;;
  esac
done

mkdir -p "$OUTPUT_DIR"

if [ "$ENGINE" = "postgres" ]; then
  COMPOSE_FILES=( -f docker-compose.postgresql.yml -f docker-compose.postgresql.dev.yml )
  DB_SERVICE=database
  echo "Creating Postgres backup to $FILE..."
  docker compose -p "$PROJECT_NAME" "${COMPOSE_FILES[@]}" exec -T "$DB_SERVICE" pg_dump -U "${DB_USER:-unisurf}" "${DB_NAME:-unisurf}" > "$FILE"
else
  COMPOSE_FILES=( -f docker-compose.mariadb.yml -f docker-compose.mariadb.dev.yml )
  DB_SERVICE=database
  echo "Creating MariaDB backup to $FILE..."
  docker compose -p "$PROJECT_NAME" "${COMPOSE_FILES[@]}" exec -T "$DB_SERVICE" sh -c "mysqldump -u\"${DB_USER:-unisurf}\" -p\"${DB_PASSWORD:-nopassword}\" \"${DB_NAME:-unisurf}\"" > "$FILE"
fi

echo "✅ Backup written: $FILE"
