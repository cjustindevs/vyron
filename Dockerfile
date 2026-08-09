# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Create default .env so artisan works at build time (values are overridden by Render env vars)
RUN cp .env.example .env

# Install PHP dependencies from scratch and VERIFY the autoloader can load Laravel core classes.
# Fails the build if the classmap is broken (prevents "Target class [files] does not exist" at runtime).
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN rm -rf vendor \
    && composer install --optimize-autoloader --no-dev --no-interaction \
    && php -r "require '/var/www/html/vendor/autoload.php'; foreach (['Illuminate\\\\Filesystem\\\\Filesystem', 'Illuminate\\\\Foundation\\\\Application', 'Illuminate\\\\Container\\\\Container', 'Illuminate\\\\Events\\\\Dispatcher'] as \\$c) { if (!class_exists(\\$c)) { fwrite(STDERR, 'AUTOLOAD ERROR: ' . \\$c . ' missing\\n'); exit(1); } } echo 'Autoload sanity check: OK\\n';" \
    && rm -f bootstrap/cache/packages.php bootstrap/cache/services.php \
    && php artisan package:discover --ansi

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install NPM dependencies and build assets
RUN npm install && npm run build

# Set permissions (Apache runs as www-data)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Apache to serve from public directory
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Expose port 10000 (Render default) - real port is set from $PORT at runtime
EXPOSE 10000

# Copy startup entrypoint (sets Apache port from $PORT, bootstraps APP_KEY)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]