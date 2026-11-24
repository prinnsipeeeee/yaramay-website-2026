#!/bin/sh
set -e

# Bind Apache dynamically to $PORT (required by Render, Railway, Koyeb, Fly.io)
PORT="${PORT:-80}"
sed -ri "s/^Listen [0-9]+/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Ensure writable runtime directories exist (including if a persistent disk is mounted)
DATA_PATH="${DATA_DIR:-/var/www/html/data}"
mkdir -p "${DATA_PATH}/sessions" /var/www/html/uploads/receipts
chown -R www-data:www-data "${DATA_PATH}" /var/www/html/uploads 2>/dev/null || true
chmod -R 775 "${DATA_PATH}" /var/www/html/uploads 2>/dev/null || true

exec "$@"
