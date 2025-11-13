#!/bin/bash

COMPOSER=composer.phar

if [ ! -f "$COMPOSER" ]; then
    echo "$COMPOSER does not exist and will be downloaded."
    wget https://getcomposer.org/download/latest-stable/composer.phar
fi

php composer.phar --no-progress --optimize-autoloader --classmap-authoritative --prefer-dist --ignore-platform-reqs --verbose "$@"
