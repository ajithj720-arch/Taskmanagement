# ============================================================
# Stage 1 — Base Image
# Official PHP 8.3 with FPM (FastCGI Process Manager)
# FPM handles PHP processing behind Nginx
# ============================================================
FROM php:8.3-fpm

# ============================================================
# Stage 2 — System Dependencies
# Install Linux packages needed by PHP extensions
# Then clean up apt cache to keep the image small
# ============================================================
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ============================================================
# Stage 3 — PHP Extensions
# Install extensions required by Laravel:
#   pdo_mysql  — MySQL database driver
#   mbstring   — Multibyte string handling
#   exif       — Image metadata reading
#   pcntl      — Process control (queue workers)
#   bcmath     — Arbitrary precision math
#   gd         — Image processing
#   zip        — ZIP file support
# ============================================================
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# ============================================================
# Stage 4 — Install Composer
# Copy Composer binary from the official Composer image
# No manual install needed
# ============================================================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ============================================================
# Stage 5 — Set Working Directory
# All subsequent commands run inside /var/www
# This is where the Laravel application lives
# ============================================================
WORKDIR /var/www

# ============================================================
# Stage 6 — Install PHP Dependencies (with layer caching)
# Copy only composer files first — Docker caches this layer
# If app code changes but dependencies don't, this is reused
# ============================================================
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --no-interaction

# ============================================================
# Stage 7 — Copy Application Code
# Copy all project files into the container
# (Done AFTER composer install for better layer caching)
# Frontend assets (public/build/) are pre-built and committed
# to the repository — no Node.js needed in the container
# ============================================================
COPY . .

# ============================================================
# Stage 8 — Optimise Autoloader
# Generate an optimised class map for faster class loading
# in production
# ============================================================
RUN composer dump-autoload --optimize

# ============================================================
# Stage 9 — Entrypoint Script
# Copy the startup script that automatically runs:
#   - Waits for MySQL to be ready
#   - Generates app key
#   - Runs migrations
#   - Seeds demo data
#   - Clears caches
#   - Starts PHP-FPM
# ============================================================
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# ============================================================
# Stage 10 — Set File Permissions
# Give www-data (web server user) write access to:
#   storage/       — logs, sessions, file uploads, cache
#   bootstrap/cache — compiled config and routes
# ============================================================
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# ============================================================
# Stage 11 — Expose Port & Set Entrypoint
# EXPOSE 9000 — PHP-FPM listens here (Nginx connects to this)
# ENTRYPOINT  — Script that runs when the container starts
# ============================================================
EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
