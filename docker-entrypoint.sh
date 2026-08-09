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

# 3) Keep cache dirs writable by the web server user
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

exec "$@"