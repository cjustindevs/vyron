#!/bin/sh
set -e

# 1) Make Apache listen on Render's $PORT (defaults to 80 for local use)
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# 2) Bootstrap APP_KEY if the platform did not provide one
if [ -z "$APP_KEY" ]; then
    if ! grep -q "^APP_KEY=base64:" /var/www/html/.env; then
        echo ">>> APP_KEY not set - generating one..."
        php artisan key:generate --force
    fi
fi

# 3) Regenerate package manifests fresh on every boot. Stale bootstrap/cache
#    manifests are a common cause of "Target class [files] does not exist".
rm -f /var/www/html/bootstrap/cache/packages.php /var/www/html/bootstrap/cache/services.php
echo ">>> Regenerating package manifests..."
php artisan package:discover --ansi

# 4) Run pending migrations (Render free tier has no shell; this is idempotent).
if [ -n "$DB_HOST" ]; then
    echo ">>> Running migrations..."
    php artisan migrate --force --no-interaction || echo ">>> migrate FAILED (non-fatal, app may still start)"
    if [ "$RUN_SEED" = "true" ]; then
        echo ">>> Seeding database..."
        php artisan db:seed --force --no-interaction || echo ">>> seed FAILED (non-fatal)"
    fi
else
    echo ">>> DB_HOST not set - skipping migrations"
fi

# 5) Keep cache dirs writable by the web server user
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

exec "$@"