#!/bin/sh
set -e

# Run storage link if not present
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link || true
fi

# Ensure storage directories exist and are writable
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/storage/untranslated
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Cache configuration, routes, and views for production performance
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Run database migrations automatically if DB is connected
php artisan migrate --force || true

# Start PHP-FPM in background (logging to stderr so Render logs capture PHP errors)
php-fpm -D -R

# Start Nginx in foreground
exec nginx -g "daemon off;"
