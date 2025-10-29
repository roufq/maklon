# Deployment Guide

This document outlines a minimal, production-oriented deployment for the Maklon (Laravel) app.

## 1) Production Environment
- PHP 8.2+, Composer, Node 18+ (build stage only)
- Web server: Nginx/Apache with PHP-FPM
- Database: MySQL/MariaDB/PostgreSQL
- Set `APP_ENV=production`, `APP_DEBUG=false`
- Storage permissions for `storage/` and `bootstrap/cache/`

## 2) Build & Optimize
```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci && npm run build
composer run prod:optimize
```

## 3) Migrations & Maintenance Mode
```bash
php artisan down --render=errors::503
php artisan migrate --force
composer run prod:optimize
php artisan up
```
Alternatively, run all via:
```bash
composer run deploy
```

## 4) Queue & Scheduler
- Queue: `php artisan queue:work --sleep=3 --tries=3 --max-time=3600`
- Scheduler (cron, every minute):
```
* * * * * /usr/bin/php /var/www/html/artisan schedule:run >> /dev/null 2>&1
```
See `scripts/cron/*.sh` and `docs/CRON_SETUP_CPANEL.md`.

## 5) Backups
Use the provided script (Linux):
```bash
scripts/backup/db-backup.sh /backups $(date +"%Y%m%d_%H%M%S")
```
Environment variables required:
`DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD`.
Schedule with cron daily. Ensure backup folder is writable and rotated.

## 6) Logging & Monitoring
- Set `.env`:
  - `LOG_CHANNEL=stack`
  - `LOG_STACK=daily`
  - `LOG_DAILY_DAYS=14`
  - `LOG_LEVEL=info`
- For alerting, configure Slack: `LOG_SLACK_WEBHOOK_URL`.
- Infrastructure monitoring: use server metrics (CPU/RAM/disk), log shipping (e.g., Papertrail).

## 7) Caching
- Use Redis for `CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION` where available.
- Warm caches after deploy: `composer run prod:optimize`.

## 8) Security
- Ensure HTTPS everywhere (HSTS), secure cookies, rate limiting enabled (default throttle in routes/api).
- Rotate API tokens in Settings > API Tokens.

## 9) Zero-Downtime (Optional)
- Use blue/green or atomic symlink switch (e.g., Deployer), run `composer run deploy` on new release,
  then switch symlink.

