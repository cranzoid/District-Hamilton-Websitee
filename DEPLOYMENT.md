# Deployment Guide — District Hamilton

Production stack: Ubuntu 22.04 · Nginx · PHP 8.2-FPM · SQLite · Certbot

---

## 1. Initial server setup (one-time)

```bash
# System packages
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx php8.2-fpm php8.2-cli php8.2-sqlite3 \
    php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd \
    git unzip curl

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node (via nvm or nodesource — Node 20 LTS)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# App directory
sudo mkdir -p /var/www/district-hamilton
sudo chown www-data:www-data /var/www/district-hamilton

# Clone repo (run as www-data or adjust ownership after)
git clone https://github.com/visheshvaibhav/District-Hamilton-Websitee /var/www/district-hamilton
cd /var/www/district-hamilton

# .env
cp .env.production.example .env
# — fill in all values (see section 3) —
php8.2 artisan key:generate

# Storage
php8.2 artisan storage:link
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 2. Nginx vhost

Place in `/etc/nginx/sites-available/district-hamilton` and symlink to `sites-enabled/`:

```nginx
server {
    listen 80;
    server_name district-tapas.com www.district-tapas.com;
    return 301 https://district-tapas.com$request_uri;
}

server {
    listen 443 ssl http2;
    server_name district-tapas.com;

    root /var/www/district-hamilton/public;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/district-tapas.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/district-tapas.com/privkey.pem;
    include             /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam         /etc/letsencrypt/ssl-dhparams.pem;

    # Redirect www → apex
    if ($host = www.district-tapas.com) {
        return 301 https://district-tapas.com$request_uri;
    }

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Issue TLS cert:
```bash
sudo certbot --nginx -d district-tapas.com -d www.district-tapas.com
```

---

## 3. Required `.env` values

Copy `.env.production.example` to `.env` and fill in every value marked `REQUIRED`.

| Variable | Notes |
|---|---|
| `APP_KEY` | Generate with `php artisan key:generate` |
| `APP_URL` | `https://district-tapas.com` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `DB_CONNECTION` | `sqlite` (default) — file lives at `database/database.sqlite` |
| `STRIPE_KEY` | Live publishable key (`pk_live_…`) |
| `STRIPE_SECRET` | Live secret key (`sk_live_…`) |
| `STRIPE_WEBHOOK_SECRET` | From Stripe Dashboard → Webhooks → signing secret |
| `MAIL_MAILER` | `smtp` (or `ses`, `mailgun`) |
| `MAIL_HOST` / `MAIL_PORT` / `MAIL_USERNAME` / `MAIL_PASSWORD` | SMTP credentials |
| `MAIL_FROM_ADDRESS` | `info@district-tapas.com` |
| `ADMIN_EMAIL` | Kitchen notification recipient |
| `QUEUE_CONNECTION` | `database` |
| `SESSION_DRIVER` | `database` |

---

## 4. Queue worker (systemd)

```bash
sudo cp /var/www/district-hamilton/scripts/laravel-queue.service \
        /etc/systemd/system/laravel-queue.service

sudo systemctl daemon-reload
sudo systemctl enable laravel-queue
sudo systemctl start laravel-queue

# Verify
sudo systemctl status laravel-queue
```

Logs are written to `/var/log/laravel-queue.log`.

---

## 5. Scheduler (cron)

Add to `www-data`'s crontab (`sudo crontab -u www-data -e`):

```
* * * * * cd /var/www/district-hamilton && php8.2 artisan schedule:run >> /dev/null 2>&1
```

This runs the daily sitemap regeneration and any other scheduled commands.

---

## 6. Stripe webhook endpoint

In the Stripe Dashboard → Developers → Webhooks, add:

- **URL:** `https://district-tapas.com/stripe/webhook`
- **Events:** `payment_intent.succeeded`, `payment_intent.payment_failed`, `charge.refunded`

Copy the signing secret into `STRIPE_WEBHOOK_SECRET` in `.env`.

---

## 7. Routine deploys

SSH into the EC2 host, then:

```bash
cd /var/www/district-hamilton
./scripts/deploy.sh
```

The script puts the site in maintenance mode, pulls latest `master`, rebuilds assets, migrates, syncs menu from `database/data/menu.json`, warms caches, and brings the site back up. Downtime is typically under 30 seconds.

To deploy a specific branch:
```bash
./scripts/deploy.sh my-feature-branch
```

---

## 8. Menu updates

On your local machine:

```bash
# Drop new PDF into storage
cp ~/Downloads/menu-2025-06.pdf storage/app/menus/incoming/

# Parse + review diff
php artisan menu:import storage/app/menus/incoming/menu-2025-06.pdf

# Commit the updated snapshot
git add database/data/menu.json
git commit -m "chore: update menu snapshot June 2025"
git push
```

On next deploy, `menu:sync` re-applies the snapshot automatically.

---

## 9. First-time database setup

```bash
touch /var/www/district-hamilton/database/database.sqlite
php8.2 artisan migrate --force
php8.2 artisan db:seed --class=AdminUserSeeder   # if seeder exists
```
