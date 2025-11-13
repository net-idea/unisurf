#!/bin/bash

# Development Script for UniSurf
# Start the Webpack Encore Dev Server and Symfony Development Server in parallel

echo "🎭 UniSurf - Development Environment"
echo "===================================================="
echo ""

# Farben für Output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Prüfe ob node_modules existiert
if [ ! -d "node_modules" ]; then
    echo -e "${YELLOW}📦 Installing Node dependencies...${NC}"
    yarn install
    if [ $? -ne 0 ]; then
        echo -e "${RED}❌ Yarn install failed!${NC}"
        exit 1
    fi
fi

# Prüfe ob vendor existiert
if [ ! -d "vendor" ]; then
    echo -e "${YELLOW}📦 Installing PHP dependencies...${NC}"
    composer install
    if [ $? -ne 0 ]; then
        echo -e "${RED}❌ Composer install failed!${NC}"
        exit 1
    fi
fi

# Detect missing dev packages (e.g. DebugBundle) after a production deploy
if [ -d "vendor" ] && [ ! -d "vendor/symfony/debug-bundle" ]; then
    echo -e "${YELLOW}Dev dependencies missing (symfony/debug-bundle not found). Re-installing with dev...${NC}"
    composer install --no-interaction
fi

# Cache leeren
echo -e "${YELLOW}🧹 Clearing cache...${NC}"
php bin/console cache:clear || {
  echo -e "${YELLOW}Cache clear failed, attempting dev composer install...${NC}";
  composer install --no-interaction;
  php bin/console cache:clear || { echo -e "${RED}Cache clear failed after reinstall.${NC}"; exit 1; };
}

# Assets bauen
echo -e "${YELLOW}🔨 Building assets...${NC}"
yarn encore dev
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Asset build failed!${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}✅ Setup complete!${NC}"
echo ""
echo "Starting development servers..."
echo ""
echo -e "${GREEN}📦 Webpack Dev Server:${NC} http://localhost:8080"
echo -e "${GREEN}🚀 Symfony Server:${NC}     http://localhost:8000"
echo ""
echo -e "${YELLOW}Press Ctrl+C to stop all servers${NC}"
echo ""

# Funktion zum Beenden aller Prozesse
cleanup() {
    echo ""
    echo -e "${YELLOW}🛑 Stopping servers...${NC}"
    kill $WEBPACK_PID $SYMFONY_PID 2>/dev/null
    exit 0
}

# Trap für Ctrl+C
trap cleanup SIGINT SIGTERM

# Starte Webpack Dev Server im Hintergrund
yarn encore dev --watch &
WEBPACK_PID=$!

# Warte kurz, damit Webpack starten kann
sleep 2

# Starte Symfony Server im Hintergrund
symfony server:start --no-tls --port=8000 &
SYMFONY_PID=$!

# Warte auf beide Prozesse
wait
