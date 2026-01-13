#!/bin/bash

# Lint Twig files
php bin/console lint:twig templates

# Type check TypeScript files
yarn run tsc:check

# Lint all files
yarn run lint:fix

# Lint PHP files
./php-cs-fixer.sh
