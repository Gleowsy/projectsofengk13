#!/usr/bin/env bash
# exit on error
set -o errexit

echo "Running composer install..."
composer install --no-dev --optimize-autoloader

echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Running migrations..."
php artisan migrate --force