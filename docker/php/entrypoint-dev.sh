#!/bin/bash
set -e

# Trust the mounted project directory in git
git config --global --add safe.directory /var/www/html

# Fix ownership of writable Laravel directories
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Execute the main command
exec "$@"
