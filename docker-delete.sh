#!/bin/bash

PROJECT_NAME="${APP_NAME:-unisurf}"

read -r -p "Delete Docker stack for project '$PROJECT_NAME' (containers, networks, volumes)? [y/N] " confirm
if [[ ! "$confirm" =~ ^[Yy]$ ]]; then
  echo "Aborted."
  exit 0
fi

echo "Stopping and removing project stack '$PROJECT_NAME'..."
docker compose -p "$PROJECT_NAME" down -v

echo "Removing database data, logs, and backups..."
rm -rf mariadb/data mariadb/log mariadb/backup
rm -rf postgresql/data postgresql/log postgresql/backup

echo "Done."
