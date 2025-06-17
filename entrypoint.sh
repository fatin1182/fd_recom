#!/bin/sh

# Wait for DB to be ready (optional: you can use health check instead in prod)
echo "⏳ Waiting for database..."
sleep 5

# Ensure Laravel key is set
if [ ! -f /var/www/bootstrap/cache/config.php ]; then
  echo "🔐 Generating app key..."
  php artisan key:generate
fi

# Clear old cache (optional but safe)
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
echo "📦 Running migrations..."
php artisan migrate --force

# Start Laravel app using PHP’s built-in server
echo "🚀 Starting Laravel app..."
php -S 0.0.0.0:${PORT:-8000} -t public
