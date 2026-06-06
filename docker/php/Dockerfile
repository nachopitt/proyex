# syntax=docker/dockerfile:1

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
FROM php:8.4-fpm-alpine AS php_wayfinder_build
WORKDIR /var/www/html

# Install system dependencies required for Node.js and PHP extensions
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
    libtool \
    nodejs \
    npm

# Install common PHP extensions for Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    gd \
    intl \
    pcntl \
    pdo_mysql \
    zip \
    exif

# Install Redis
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

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
FROM php:8.4-fpm-alpine AS prod
WORKDIR /var/www/html

# Install system dependencies for runtime (minimal)
RUN apk add --no-cache \
    bash \
    git \
    libzip-dev \
    mysql-client \
    libpng \
    jpeg \
    freetype \
    libwebp \
    zlib \
    icu-libs \
    libtool-libs 

# Install common PHP extensions for Laravel (runtime)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    gd \
    intl \
    pcntl \
    pdo_mysql \
    zip \
    exif

# Install Redis (runtime)
RUN pecl install redis && docker-php-ext-enable redis

# Copy application source code
COPY . .

# Copy Composer dependencies from the composer_install stage
COPY --from=composer_install /app/vendor /var/www/html/vendor

# Copy built frontend assets from the node_build stage
COPY --from=node_build /var/www/html/public/build /var/www/html/public/build

# Copy the generated Wayfinder files from the php_wayfinder_build stage
# Assuming Wayfinder generates into resources/js/wayfinder, actions, routes
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
# =============================================================================
# This stage is for the FPM container in the development environment.
# It adds Xdebug. IT DOES NOT COPY ANY APPLICATION CODE.
FROM php:8.4-fpm-alpine AS dev

# Install and configure Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN echo "xdebug.mode=develop,debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini


# =============================================================================
# Workspace Stage
# =============================================================================
# This stage is for the workspace/CLI container.
# It's based on the 'dev' image to ensure it has all the tools.
FROM dev AS workspace