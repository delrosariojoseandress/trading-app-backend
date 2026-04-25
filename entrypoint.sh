#!/bin/bash

echo "======================================"
echo " Laravel Trading App Starting"
echo "======================================"

echo "Waiting for services..."

sleep 5

echo "Render DB detected - NO AUTO MIGRATION (SAFE MODE)"

# Laravel optimizations only
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."

exec apache2-foreground