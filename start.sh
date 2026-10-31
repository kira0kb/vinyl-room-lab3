#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"

if [ ! -f vendor/autoload.php ] || [ ! -f .env ] || [ ! -f database/database.sqlite ]; then
    bash setup.sh
fi
if [ ! -e public/storage ]; then php artisan storage:link --force; fi
echo "Открой http://127.0.0.1:8000. Для остановки нажми Ctrl+C."
php artisan serve --host=127.0.0.1 --port=8000
