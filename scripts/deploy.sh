#!/usr/bin/env bash
# Usage: ./scripts/deploy.sh [branch]
# Run on the EC2 host after ssh'ing in. Defaults to origin/master.
set -euo pipefail

BRANCH="${1:-master}"
APP_DIR="/var/www/district-hamilton"
PHP="php8.2"

echo "==> Deploying branch: ${BRANCH}"

cd "$APP_DIR"

# ── Maintenance mode ──────────────────────────────────────────────────────────
$PHP artisan down --retry=60

# ── Pull latest code ──────────────────────────────────────────────────────────
git fetch --all
git reset --hard "origin/${BRANCH}"

# ── PHP dependencies ──────────────────────────────────────────────────────────
composer install --no-dev --optimize-autoloader --no-interaction

# ── JS/CSS build ──────────────────────────────────────────────────────────────
npm ci --omit=dev
npm run build

# ── Database ──────────────────────────────────────────────────────────────────
$PHP artisan migrate --force

# ── Menu sync from committed menu.json ───────────────────────────────────────
if [ -f database/data/menu.json ]; then
    $PHP artisan menu:sync --force
fi

# ── Storage ──────────────────────────────────────────────────────────────────
$PHP artisan storage:link 2>/dev/null || true

# ── Cache warm-up ─────────────────────────────────────────────────────────────
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan event:cache

# ── Sitemap ───────────────────────────────────────────────────────────────────
$PHP artisan sitemap:generate

# ── Restart services ─────────────────────────────────────────────────────────
sudo systemctl reload php8.2-fpm
sudo systemctl restart laravel-queue

# ── Back online ───────────────────────────────────────────────────────────────
$PHP artisan up

echo "==> Deploy complete."
