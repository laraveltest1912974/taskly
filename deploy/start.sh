#!/bin/sh
set -e

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY is not set. Generate one with: php artisan key:generate --show" >&2
    exit 1
fi

sed "s/__PORT__/${PORT:-10000}/g" /etc/nginx/templates/default.conf > /etc/nginx/http.d/default.conf

php artisan migrate --force --no-interaction

# Optional demo content: DEMO_SEED_EMAILS=a@example.com,b@example.com (existing users). The command skips users who
# already have tasks, so it is safe to leave the variable set; a failure must not stop the app from starting.
if [ -n "${DEMO_SEED_EMAILS:-}" ]; then
    php artisan taskly:seed-demo $(printf '%s' "$DEMO_SEED_EMAILS" | tr ',' ' ') --no-interaction || echo "Demo seeding failed, continuing." >&2
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec /usr/bin/supervisord -c /etc/supervisord.conf
