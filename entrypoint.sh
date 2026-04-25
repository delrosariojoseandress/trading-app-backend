#!/bin/bash

echo "======================================"
echo " Starting Laravel Trading Container"
echo "======================================"

# Wait for database (safe startup)
echo "Waiting for database..."

until nc -z db 5432; do
  echo "DB not ready yet..."
  sleep 2
done

echo "Database ready!"

# Run migrations safely
php artisan migrate --force

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."

exec apache2-foreground