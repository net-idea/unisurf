#!/usr/bin/env bash
set -euo pipefail

# Format all project files according to .editorconfig & Prettier, excluding .gitignore paths.
# Requires: prettier (npm install), php-cs-fixer (composer) if PHP formatting desired.

PROJECT_ROOT="$(cd "$(dirname "$0")" && pwd)"
cd "$PROJECT_ROOT"

if ! command -v npx >/dev/null 2>&1; then
  echo "npx not found. Install Node.js / npm first." >&2
  exit 1
fi

# Build an array of ignore patterns derived from .gitignore we do not want to traverse.
IGNORE_PATTERNS=(
  'vendor'
  'node_modules'
  'public/build'
  'public/bundles'
  'var'
  'mariadb/backup'
  'mariadb/data'
  'mariadb/log'
  'postgresql/backup'
  'postgresql/data'
  'redis/data'
  'certs'
  'openssl'
  '.idea'
  '.vscode'
)

# File extensions to format (Prettier supported + Twig via plugin)
EXTENSIONS=(js ts json css scss html htm twig yml yaml md vue)

# Build find command with exclusions and file extensions
FIND_CMD="find ."

# Add prune expressions for each ignore pattern
for pat in "${IGNORE_PATTERNS[@]}"; do
  FIND_CMD+=" -path './${pat}' -prune -o"
done

# Add file type expression
FIND_CMD+=" \\( "
first=1
for ext in "${EXTENSIONS[@]}"; do
  if [ $first -eq 1 ]; then
    FIND_CMD+="-name '*.${ext}'"
    first=0
  else
    FIND_CMD+=" -o -name '*.${ext}'"
  fi
done
FIND_CMD+=" \\) -type f -print"

# Run find and collect files
mapfile -t FILES < <(eval "$FIND_CMD")

if [ ${#FILES[@]} -gt 0 ]; then
  # Run prettier --write on batches to avoid command line length issues
  BATCH_SIZE=200

  echo "Formatting ${#FILES[@]} files with Prettier..."
  for ((i=0; i<${#FILES[@]}; i+=BATCH_SIZE)); do
    batch=("${FILES[@]:i:BATCH_SIZE}")
    npx prettier --config .prettierrc.json --write "${batch[@]}"
  done
else
  echo "No Prettier-eligible files found." >&2
fi

# PHP formatting if php-cs-fixer is available or installed locally
if command -v php >/dev/null 2>&1; then
  if [ ! -d php-cs-fixer/vendor ]; then
    echo "Installing php-cs-fixer locally..."
    mkdir -p php-cs-fixer
    composer require --quiet --working-dir=php-cs-fixer friendsofphp/php-cs-fixer >/dev/null 2>&1 || {
      echo "Failed to install php-cs-fixer" >&2
    }
  fi
  if [ -f php-cs-fixer/vendor/bin/php-cs-fixer ]; then
    echo "Formatting PHP files with php-cs-fixer..."
    php-cs-fixer/vendor/bin/php-cs-fixer fix src tests --config .php-cs-fixer.php --quiet || echo "php-cs-fixer encountered issues" >&2
  fi
fi

echo "Formatting completed."
