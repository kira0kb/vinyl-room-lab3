#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"

for tool in php composer node npm; do
    if ! command -v "$tool" >/dev/null 2>&1; then
        echo "Не найдена команда $tool. Открой раздел установки в README.md."
        exit 1
    fi
done

php -r 'if (version_compare(PHP_VERSION, "8.3.0", "<")) { fwrite(STDERR, "Нужен PHP 8.3 или новее.\n"); exit(1); }'
php -r 'foreach (["gd", "pdo_sqlite", "mbstring", "fileinfo", "dom", "xml"] as $ext) { if (!extension_loaded($ext)) { fwrite(STDERR, "В PHP не включено расширение: $ext\n"); exit(1); } }'

if [ ! -f .env ]; then cp .env.example .env; fi
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
composer install --prefer-dist --no-interaction
php artisan config:clear

# При повторной установке сохраняем ключ и уже введённые данные.
if ! grep -Eq '^APP_KEY=.+$' .env; then php artisan key:generate; fi
if [ ! -f database/database.sqlite ]; then touch database/database.sqlite; fi
php artisan migrate --seed --force
if [ ! -e public/storage ]; then php artisan storage:link --force; fi

npm ci --no-audit --no-fund
npm run build
echo "Готово. Запусти: bash start.sh"
