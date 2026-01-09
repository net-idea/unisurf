#!/bin/bash

# Usage: ./docker-yarn encore dev --watch

# Get environment variables
if [ -f .env ]; then
  set -o allexport
  source .env
  set +o allexport
fi

# Dynamic container naming based on APP_NAME from .env
APP_NAME="${APP_NAME:-unisurf}"
PROJECT_NAME="$APP_NAME"
CONTAINER_NAME="${PROJECT_NAME}_php"

# Ensure Docker and the target container are running
if ! command -v docker >/dev/null 2>&1 || ! docker compose version >/dev/null 2>&1; then
  echo "Docker Compose v2 is required. Please start Docker and the dev stack." >&2
  exit 1
fi

if ! docker compose ps "$CONTAINER_NAME" --format json 2>/dev/null | grep -q '"State":"running"'; then
  echo "Container $CONTAINER_NAME is not running. Start it with: docker compose -p ${PROJECT_NAME} up -d" >&2
  exit 1
fi

# Always run in Docker
echo "🐳 Running Yarn command in Docker..."
docker compose -p "$PROJECT_NAME" exec "$CONTAINER_NAME" yarn "$@"
