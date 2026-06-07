# syntax=docker/dockerfile:1

# =============================================================================
# Stage: base_php
# Common PHP 8.4-FPM with system dependencies (used by all PHP stages)
# =============================================================================
FROM php:8.4-fpm-alpine AS base_php
WORKDIR /var/www/html

# Install system dependencies shared by all stages
RUN apk add --no-cache \
    bash \
    git \
    libzip-dev \
    mysql-dev \
    libpng-dev \
    jpeg-dev \
    freetype-dev \
    libwebp-dev \
    zlib-dev \
    icu-dev \
    libtool

# Copy the extension installer from a container image to avoid flaky remote URL fetches
COPY --from=ghcr.io/mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

# Install all common PHP extensions (used by prod, dev, and php_wayfinder_build)
RUN install-php-extensions pdo_mysql zip gd intl pcntl exif redis

# =============================================================================
# Stage 1: composer_install
# Installs PHP Composer dependencies
# =============================================================================
FROM composer:latest AS composer_install
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-autoloader --optimize-autoloader

# =============================================================================
# Stage 2: php_wayfinder_build
# Prepares PHP environment, copies application code, and generates Wayfinder files.
# This stage ensures PHP is available for artisan commands.
# =============================================================================
FROM base_php AS php_wayfinder_build

# Install Node.js for Wayfinder generation (if needed)
RUN apk add --no-cache nodejs npm

# Copy application source code
COPY . .

# Copy Composer dependencies from the first stage
COPY --from=composer_install /app/vendor /var/www/html/vendor

# Ensure .env is present for Artisan commands (even a minimal one)
# We'll copy .env.example and generate a key.
COPY .env.example .env
RUN php artisan key:generate

# Clear caches and run Wayfinder generation
RUN php artisan optimize:clear

# =============================================================================
# Stage 3: node_build
# Builds frontend assets with Vite.
# =============================================================================
FROM node:lts-alpine AS node_build
WORKDIR /var/www/html

# Copy application source code (including the Wayfinder generated files from the previous stage)
COPY . .

# Copy Composer dependencies (if npm scripts need access to vendor/autoload.php for some reason)
COPY --from=composer_install /app/vendor /var/www/html/vendor

# Install Node.js dependencies
RUN npm ci

# Run Vite build
RUN npm run build

# =============================================================================
# Final Stage: prod (Production image)
# Combines necessary artifacts for a lean production image.
# =============================================================================
FROM base_php AS prod

# Add runtime-only dependencies (minimal)
RUN apk add --no-cache \
    mysql-client

# Copy application source code
COPY . .

# Copy Composer dependencies from the composer_install stage
COPY --from=composer_install /app/vendor /var/www/html/vendor

# Copy built frontend assets from the node_build stage
COPY --from=node_build /var/www/html/public/build /var/www/html/public/build

# Copy the generated Wayfinder files from the php_wayfinder_build stage
COPY --from=php_wayfinder_build /var/www/html/resources/js/wayfinder /var/www/html/resources/js/wayfinder
COPY --from=php_wayfinder_build /var/www/html/resources/js/actions /var/www/html/resources/js/actions
COPY --from=php_wayfinder_build /var/www/html/resources/js/routes /var/www/html/resources/js/routes

# Generate the autoloader and optimize
RUN composer dump-autoload --optimize --no-dev

# Set permissions for storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

# =============================================================================
# Development Stage
# Adds Xdebug and Node.js to base_php for local development
# =============================================================================
FROM base_php AS dev

# Copy Node.js and npm so the dev image can run Vite
COPY --from=node:lts-alpine /usr/local/bin/node /usr/local/bin/node
COPY --from=node:lts-alpine /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -sf /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

# Install Xdebug only in dev (not in prod or php_wayfinder_build)
RUN install-php-extensions xdebug

# Configure Xdebug
RUN echo "xdebug.mode=develop,debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Copy entrypoint script to fix permissions on every container start
COPY docker/php/entrypoint-dev.sh /entrypoint-dev.sh
RUN chmod +x /entrypoint-dev.sh

ENTRYPOINT ["/entrypoint-dev.sh"]
CMD ["php-fpm"]

# =============================================================================
# Workspace Stage
# =============================================================================
# This stage is for the workspace/CLI container.
# It's based on the 'dev' image to ensure it has all the tools.
FROM dev AS workspace

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
