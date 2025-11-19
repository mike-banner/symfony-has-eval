#!/bin/bash
set -e

# Fix permissions (optionnel selon ton OS)
chown -R www-data:www-data /srv/app || true

# If vendor not present, install dependencies
if [ ! -d "vendor" ]; then
  if [ -f composer.json ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction --prefer-dist
  fi
fi

# Execute whatever command is passed (nice for dev)
if [ $# -gt 0 ]; then
  exec "$@"
else
  php-fpm
fi
