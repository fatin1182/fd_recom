#!/bin/sh

echo "⏳ Waiting for database..."
sleep 5

echo "🔐 Generating app key..."
php artisan key:generate --force

# Clear and cache config
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache

# Link storage (optional but good)
php artisan storage:link || true

echo "📦 Running migrations..."
php artisan migrate --force

echo "🚀 Starting Laravel app..."
exec php -S 0.0.0.0:${PORT:-8000} -t public


