# syntax=docker/dockerfile:1

# =============================================================================
# Stage: base_php
# Common PHP 8.4-FPM with system dependencies (used by all PHP stages)
# =============================================================================
FROM php:8.4-fpm-alpine AS base_php
ARG APP_VERSION=
ENV APP_VERSION=${APP_VERSION}
WORKDIR /var/www/html

# Copy host CA certificates if they exist (optional copy)
COPY docker/certs* /usr/local/share/ca-certificates/
RUN if [ -f /usr/local/share/ca-certificates/ca-certificate.crt ]; then cat /usr/local/share/ca-certificates/ca-certificate.crt >> /etc/ssl/certs/ca-certificates.crt; fi

# Install system dependencies shared by all stages
RUN apk add --no-cache \
    bash \
    git \
    libzip-dev \
    mysql-client \
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

# Copy host CA certificates if they exist (optional copy)
COPY docker/certs* /usr/local/share/ca-certificates/
RUN if [ -f /usr/local/share/ca-certificates/ca-certificate.crt ]; then cat /usr/local/share/ca-certificates/ca-certificate.crt >> /etc/ssl/certs/ca-certificates.crt; fi

COPY composer.json composer.lock ./
# Generate the autoloader here so `php artisan` can boot in frontend_build.
# (The prod stage re-dumps an optimized autoloader once the full source is present.)
RUN composer install --no-dev --no-scripts --optimize-autoloader

# =============================================================================
# Stage 2: frontend_build
# Builds the frontend with Vite. The Wayfinder Vite plugin shells out to
# `php artisan`, so this stage needs BOTH PHP (from base_php) and Node. It is a
# fat, throwaway build stage — none of its toolchain reaches the final image.
# =============================================================================
FROM base_php AS frontend_build

# Install Node.js so Vite can run alongside PHP
RUN apk add --no-cache nodejs npm

# Copy application source code
COPY . .

# Copy Composer dependencies from the first stage (vendor/autoload is needed by
# artisan, which the Wayfinder plugin invokes during the Vite build)
COPY --from=composer_install /app/vendor /var/www/html/vendor

# Ensure .env is present so artisan can boot during the build
COPY .env.example .env
RUN php artisan key:generate

# Install Node dependencies and build assets. The Wayfinder plugin runs
# `php artisan wayfinder:generate` here, where php is available.
RUN npm ci && npm run build

# =============================================================================
# Final Stage: prod (Production image)
# Combines necessary artifacts for a lean production image.
# =============================================================================
FROM base_php AS prod

# Copy application source code
COPY . .

# Copy Composer dependencies from the composer_install stage
COPY --from=composer_install /app/vendor /var/www/html/vendor

# Copy built frontend assets from the frontend_build stage. Only the compiled
# output is needed at runtime — no Node, npm, or TS source ships in prod.
COPY --from=frontend_build /var/www/html/public/build /var/www/html/public/build

# Generate the optimized autoloader with the full application source present so
# the classmap covers app/, database/, etc. Composer is copied in just for this
# build step; it is not part of the runtime.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --no-dev \
    && rm -f /usr/bin/composer

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

# Install Xdebug only in dev (not in prod or frontend_build)
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
# Stage: schemalex_builder
# Build the schemalex binary from source using Go
# =============================================================================
FROM golang:alpine AS schemalex_builder
RUN go install github.com/schemalex/schemalex/cmd/schemalex@latest

# =============================================================================
# Stage: atlas_builder
# Download Atlas CLI using the official installation script
# =============================================================================
FROM alpine:latest AS atlas_builder
RUN apk add --no-cache curl
RUN curl -sSf https://atlasgo.sh | sh

# =============================================================================
# Workspace Stage
# =============================================================================
# This stage is for the workspace/CLI container.
# It's based on the 'dev' image to ensure it has all the tools.
FROM dev AS workspace

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install schemalex
COPY --from=schemalex_builder /go/bin/schemalex /usr/local/bin/schemalex

# Install Atlas CLI
COPY --from=atlas_builder /usr/local/bin/atlas /usr/local/bin/atlas

# =============================================================================
# Web Stage (nginx)
# =============================================================================
# Serves static assets and proxies PHP requests to the php-fpm `app` container.
# It reuses the single `frontend_build` stage (build-once) so the compiled
# assets here are byte-identical to what the app image ships. The document root
# lives at the SAME absolute path as the app container (/var/www/html/public)
# so nginx's SCRIPT_FILENAME points at a path php-fpm can open.
FROM nginx:alpine AS web

# Web server config
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Compiled front-end assets, served directly by nginx. index.php is included so
# the front controller path exists in this image (used as the fastcgi target).
COPY --from=frontend_build /var/www/html/public /var/www/html/public

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
