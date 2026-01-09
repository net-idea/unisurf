#!/bin/bash

PROJECT_NAME="${APP_NAME:-unisurf}"
DB_ENGINE="${DB_ENGINE:-mariadb}"

if [ "$DB_ENGINE" = "postgres" ]; then
  COMPOSE_FILES=(-f docker-compose.yaml -f docker-compose.dev.yaml -f docker-compose.postgresql.yml -f docker-compose.adminer.yml -f docker-compose.phpmyadmin.yml)
  DB_SERVICE=postgres
else
  COMPOSE_FILES=(-f docker-compose.yaml -f docker-compose.dev.yaml -f docker-compose.mariadb.yml -f docker-compose.mariadb.dev.yml -f docker-compose.adminer.yml -f docker-compose.phpmyadmin.yml)
  DB_SERVICE=database
fi

if ! command -v docker >/dev/null 2>&1 || ! docker compose version >/dev/null 2>&1; then
  echo "Docker Compose v2 is required. Please start Docker." >&2
  exit 1
fi

if ! docker compose -p "$PROJECT_NAME" "${COMPOSE_FILES[@]}" ps --format json >/dev/null 2>&1; then
  echo "Stack '$PROJECT_NAME' not found or docker compose failed." >&2
  exit 1
fi

echo "Checking services for project '$PROJECT_NAME'..."
services=(php $DB_SERVICE node adminer phpmyadmin)
status=0
for svc in "${services[@]}"; do
  state=$(docker compose -p "$PROJECT_NAME" "${COMPOSE_FILES[@]}" ps "$svc" --format json 2>/dev/null | grep -o '"State":"[^\"]*"' | cut -d'"' -f4)
  if [ -z "$state" ]; then
    echo "❌ $svc missing"
    status=1
    continue
  fi
  if [ "$state" != "running" ]; then
    echo "❌ $svc not running (state: ${state:-missing})"
    status=1
    continue
  fi
  if [ "$svc" = "database" ]; then
    health=$(docker compose -p "$PROJECT_NAME" "${COMPOSE_FILES[@]}" ps "$svc" --format json 2>/dev/null | grep -o '"Health":"[^\"]*"' | cut -d'"' -f4)
    if [ "$health" != "healthy" ]; then
      echo "❌ database health: ${health:-unknown}"
      status=1
      continue
    fi
    if ! docker compose -p "$PROJECT_NAME" "${COMPOSE_FILES[@]}" exec -T "$svc" sh -c "mysqladmin ping -h127.0.0.1 -p\"${DB_ROOT_PASSWORD:-nopassword}\"" >/dev/null 2>&1; then
      echo "❌ database not responding to mysqladmin ping"
      status=1
      continue
    fi
  fi
  if [ "$svc" = "postgres" ]; then
    ready=$(docker compose -p "$PROJECT_NAME" "${COMPOSE_FILES[@]}" exec -T "$svc" pg_isready -U "${POSTGRES_USER:-unisurf}" 2>/dev/null | grep -c "accepting connections")
    if [ "$ready" -eq 0 ]; then
      echo "❌ postgres not ready"
      status=1
      continue
    fi
  fi
  echo "✅ $svc running"
done

if [ $status -eq 0 ]; then
  echo "All services are healthy."
else
  echo "Some services are not healthy."
fi

exit $status
