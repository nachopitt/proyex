#!/bin/sh
set -e

# Change to the application directory
cd /var/www/html

# 1. Environment setup
# If .env doesn't exist, copy it from .env.example
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# 2. Generate App Key
# Generate a new key only if one doesn't exist
if [ -z "$(grep -E '^APP_KEY=' .env)" ] || [ "$(grep -E '^APP_KEY=' .env | cut -d '=' -f2-)" == "" ]; then
    echo "Generating application key..."
    php artisan key:generate
else
    echo "Application key already exists."
fi

# 3. Clear caches
php artisan optimize:clear

# 4. Wait for DB
# Wait for the database to be ready
until php artisan db:monitor --quiet; do
  echo 'Waiting for database connection...'
  sleep 5
done
echo "Database is ready."

# 5. Run migrations and seeders
echo "Running database migrations..."
php artisan migrate --force

echo "Running database seeder..."
php artisan db:seed --force

# 6. Generate Wayfinder types
echo "Generating Wayfinder types..."
php artisan wayfinder:generate --with-form

echo "Startup script finished."

# 7. Execute the main container command (CMD)
exec "$@"
