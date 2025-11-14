# Docker development and installation

This guide covers installing Docker and running the project with Docker Compose. Use Docker if you want a reproducible environment without installing PHP/Node locally.

## Install Docker

### macOS

- Install Docker Desktop for Mac (recommended):

```bash
brew install --cask docker
```

Or download manually: https://www.docker.com/products/docker-desktop

### Ubuntu

```bash
# Install Docker Engine
sudo apt-get update
sudo apt-get install -y apt-transport-https ca-certificates curl software-properties-common
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io

# Use Docker without sudo
sudo usermod -aG docker $USER
newgrp docker

# Install Docker Compose plugin
sudo apt-get install -y docker-compose-plugin
```

## Start the app with Docker

From the project root (`huette9.de/`):

```bash
# Build and start containers
docker compose up -d

# Check container status
docker compose ps
```

Open the app at:

```
http://localhost:8000
```

## Database migrations (Docker)

```bash
# Create a new migration (if needed)
docker compose exec web php bin/console make:migration

# Run migrations
docker compose exec web php bin/console doctrine:migrations:migrate --no-interaction
```

## Useful Docker commands

```bash
# Stop containers
docker compose down

# View logs
docker compose logs -f

# Shell into the web container
docker compose exec web bash

# Rebuild images from scratch
docker compose build --no-cache
```

## Troubleshooting (Docker)

- Port 8000 already in use
  - Change the port mapping in `compose.yaml` (e.g. "8080:8000") or stop the conflicting service.

- Container does not start
  - Check logs: `docker compose logs web` and `docker compose logs mariadb`
  - Rebuild containers: `docker compose down && docker compose build --no-cache && docker compose up -d`

- Permissions problems on Linux
  - Adjust ownership: `sudo chown -R $USER:$USER .`
  - Ensure cache directory is writable: `chmod -R 777 var/`

- Database connection errors
  - See Database troubleshooting: `docs/database.md`
