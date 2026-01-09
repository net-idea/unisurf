#!/bin/bash

### Development environment helper
# This script helps starting a development environment in two modes:
# 1) Local mode (default): uses host PHP/Node/Composer/Yarn. It:
#    - starts MariaDB via Docker compose (dev DB compose files)
#    - installs Composer and Yarn dependencies on host
#    - clears and warms Symfony cache
#    - optionally runs Doctrine migrations
#    - starts Webpack Encore in watch mode and the PHP built-in server
# 2) Docker Compose mode: brings up the full dev stack using Docker Compose
#    (PHP, Nginx, Node watcher, MariaDB, Mailpit).
#
# Usage: ./develop.sh
# Press Ctrl+C to stop local watchers; use `docker compose -p unisurf down`
# to stop the Docker Compose stack.

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

PROJECT_DIR=$(pwd)
PHP_BIN=$(which php 2>/dev/null || echo "")
COMPOSER_BIN=$(which composer 2>/dev/null || echo "")
SYMFONY_BIN=$(which symfony 2>/dev/null || echo "")
NODE_BIN=$(which node 2>/dev/null || echo "")
YARN_BIN=$(which yarn 2>/dev/null || echo "")
DOCKER_BIN=$(which docker 2>/dev/null || echo "")

require_docker() {
    if [ -z "$DOCKER_BIN" ]; then
        echo -e "${RED}Docker is not installed or not in PATH.${NC}" >&2
        exit 1
    fi
    if ! docker compose version >/dev/null 2>&1; then
        echo -e "${RED}Docker Compose v2 is required (docker compose).${NC}" >&2
        exit 1
    fi
}

run_docker_stack() {
    require_docker
    echo -e "${YELLOW}Starting Docker dev stack (PHP, Nginx, Node watcher, MariaDB, Mailer)...${NC}"
    docker compose -p unisurf \
        -f docker-compose.yaml \
        -f docker-compose.dev.yaml \
        -f docker-compose.mariadb.yml \
        -f docker-compose.mariadb.dev.yml \
        up -d --build
    echo -e "${GREEN}Docker dev stack is running.${NC}"
    echo -e "${YELLOW}Services:${NC}"
    echo -e " - App: http://localhost:8000"
    echo -e " - Encore dev server: http://localhost:8080"
    echo -e " - Mailpit: http://localhost:8025"
    echo -e " - MariaDB: localhost:3306"
    exit 0
}

run_local_stack() {
    echo -e "${GREEN}Starting local Symfony + Webpack Encore development environment...${NC}"
    echo -e "${YELLOW}Checking dependencies...${NC}"

    if [ -z "$PHP_BIN" ]; then
        echo -e "${RED}PHP is not installed or not in PATH.${NC}" >&2
        exit 1
    fi

    if [ -z "$SYMFONY_BIN" ]; then
        echo -e "${RED}Symfony CLI is not installed. Install with: curl -sS https://get.symfony.com/cli/installer | bash${NC}" >&2
        exit 1
    fi

    if [ -z "$COMPOSER_BIN" ]; then
        echo -e "${RED}Composer is not installed.${NC}" >&2
        exit 1
    fi

    if [ -z "$NODE_BIN" ]; then
        echo -e "${RED}Node.js is not installed.${NC}" >&2
        exit 1
    fi

    if [ -z "$YARN_BIN" ]; then
        echo -e "${RED}Yarn is not installed. Install with: npm install -g yarn${NC}" >&2
        exit 1
    fi

    require_docker
    echo -e "${GREEN}All dependencies are available.${NC}"

    echo -e "${YELLOW}Starting MariaDB (Docker)...${NC}"
    docker compose -p unisurf -f docker-compose.mariadb.yml -f docker-compose.mariadb.dev.yml up -d

    echo -e "${YELLOW}Running Composer install...${NC}"
    $COMPOSER_BIN install --working-dir="$PROJECT_DIR" --prefer-dist --no-progress --no-suggest
    if [ $? -ne 0 ]; then
        echo -e "${RED}Composer install failed.${NC}" >&2
        exit 1
    fi

    echo -e "${YELLOW}Running Yarn install...${NC}"
    $YARN_BIN install --cwd "$PROJECT_DIR"
    if [ $? -ne 0 ]; then
        echo -e "${RED}Yarn install failed.${NC}" >&2
        exit 1
    fi

    echo -e "${YELLOW}Clearing and warming up cache...${NC}"
    $PHP_BIN bin/console cache:clear --no-warmup
    $PHP_BIN bin/console cache:warmup

    # Detect whether there are any migration files (handles filenames safely)
    if [ -d "migrations" ] && [ -n "$(find migrations -maxdepth 1 -type f -name '*.php' -print -quit 2>/dev/null)" ]; then
        echo
        echo -e "${YELLOW}Migrations found. Run them now? (y/N)${NC}"
        read -r response
        if [[ "$response" =~ ^[Yy]$ ]]; then
            echo -e "${YELLOW}Running migrations...${NC}"
            $PHP_BIN bin/console doctrine:migrations:migrate --no-interaction
        else
            echo -e "${GREEN}Migrations skipped.${NC}"
        fi
    else
        echo -e "${GREEN}No migrations found.${NC}"
    fi

    trap 'echo -e "${YELLOW}Stopping development environment...${NC}"; kill $YARN_PID $PHP_PID 2>/dev/null; exit 0' INT TERM

    echo
    echo -e "${GREEN}Starting development servers...${NC}"
    echo -e "${YELLOW}   → Yarn Encore: http://localhost:8080 (assets)${NC}"
    echo -e "${YELLOW}   → PHP Server:  http://127.0.0.1:8000${NC}"
    echo -e "${YELLOW}Press Ctrl+C to stop.${NC}"
    echo

    $YARN_BIN encore dev-server --host 0.0.0.0 --port 8080 --hot &
    YARN_PID=$!

    sleep 2

    $PHP_BIN -S 127.0.0.1:8000 -t public &
    PHP_PID=$!

    wait
}

echo -e "${YELLOW}Select environment:${NC}"
echo "1) Local (host tools) + MariaDB via Docker"
echo "2) Docker Compose (full dev stack)"
read -rp "Choice [1/2]: " ENV_CHOICE

case "$ENV_CHOICE" in
    2) run_docker_stack ;;
    *) run_local_stack ;;
esac
