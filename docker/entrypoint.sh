#!/bin/sh
set -e

# Run storage link if not present
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link || true
fi

# Cache configuration, routes, and views for production performance
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Run database migrations automatically if DB is connected
php artisan migrate --force || true

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
exec nginx -g "daemon off;"
