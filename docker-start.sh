#!/bin/bash

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

trap 'echo -e "${YELLOW}Stopping containers...${NC}"; docker-compose down; exit 0' INT TERM

echo -e "${GREEN}Starting Docker development environment...${NC}"

# Start services
docker-compose up -d

# Wait for database
echo -e "${YELLOW}Waiting for database...${NC}"
until docker-compose exec database mysqladmin ping -h"database" --silent; do
    sleep 1
done

# Run Composer install (if not done)
echo -e "${YELLOW}Running Composer install...${NC}"
docker-compose exec php composer install --prefer-dist --no-progress

# Run Yarn install
echo -e "${YELLOW}Running Yarn install...${NC}"
docker-compose run --rm node yarn install

# Clear & warmup cache
echo -e "${YELLOW}Clearing cache...${NC}"
docker-compose exec php php bin/console cache:clear --no-warmup
docker-compose exec php php bin/console cache:warmup

# Migrations
if [ -d "migrations" ] && [ "$(ls -A migrations/*.php 2>/dev/null | wc -l)" -gt 0 ]; then
    echo
    echo -e "${YELLOW}Migrations found. Run them? (y/N)${NC}"
    read -r response
    if [[ "$response" =~ ^[Yy]$ ]]; then
        echo -e "${YELLOW}Running migrations...${NC}"
        docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
    fi
fi

# Start Yarn watch in node service
echo -e "${GREEN}Starting Yarn Encore watch...${NC}"
docker-compose up -d node

echo
echo -e "${GREEN}Development environment is running!${NC}"
echo -e "${YELLOW}   → App: http://localhost:8000${NC}"
echo -e "${YELLOW}   → Assets: http://localhost:8080 (via Yarn)${NC}"
echo -e "${YELLOW}Press Ctrl+C to stop.${NC}"

# Keep script alive
tail -f /dev/null
