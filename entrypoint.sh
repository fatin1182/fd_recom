#!/bin/sh

echo "⏳ Waiting for database..."
sleep 5

# Set key if not cached
if [ ! -f /var/www/storage/oauth-private.key ]; then
  echo "🔐 Generating app key..."
  php artisan key:generate --force
fi

# Clear and cache configuration
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache

# Run migrations
echo "📦 Running migrations..."
php artisan migrate --force

# Start Laravel server
echo "🚀 Starting Laravel app..."
exec php -S 0.0.0.0:${PORT:-8000} -t public

