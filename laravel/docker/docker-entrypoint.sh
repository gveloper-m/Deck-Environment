#!/bin/sh
set -e

# If artisan is missing, create a fresh Laravel app into the mounted app folder.
if [ ! -f /var/www/html/artisan ]; then
  echo "No Laravel app found in /var/www/html. Creating a fresh Laravel project..."
  composer create-project --prefer-dist laravel/laravel /tmp/laravel
  echo "Copying project into mounted folder..."
  # copy while preserving existing files (if any). Use rsync-like fallback.
  cp -a /tmp/laravel/. /var/www/html/
  rm -rf /tmp/laravel
  chown -R www-data:www-data /var/www/html || true
fi

cd /var/www/html

# Ensure composer deps exist
if [ ! -d vendor ]; then
  composer install --no-interaction --prefer-dist || true
fi

# Ensure .env exists and DB settings are set up for Postgres
if [ ! -f .env ]; then
  cp .env.example .env || true
fi

sed -i "s/DB_CONNECTION=mysql/DB_CONNECTION=pgsql/g" .env || true
sed -i "s/DB_PORT=3306/DB_PORT=5432/g" .env || true
sed -i "s/DB_DATABASE=laravel/DB_DATABASE=laravel_db/g" .env || true
sed -i "s/DB_USERNAME=root/DB_USERNAME=laravel_user/g" .env || true
sed -i "s/DB_PASSWORD=/DB_PASSWORD=laravel_pass/g" .env || true

# Generate app key if missing
if ! php artisan key:generate --no-interaction >/dev/null 2>&1; then
  php artisan key:generate --no-interaction || true
fi

# Add a simple API route (overwrites default routes/api.php with a tiny example)
if [ ! -f routes/api.php ] || ! grep -q "Hello from Laravel API" routes/api.php 2>/dev/null; then
  cat > routes/api.php <<'PHP'
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->get('/hello', function (Request $request) {
    return response()->json(['message' => 'Hello from Laravel API']);
});
PHP
fi

exec "$@"
