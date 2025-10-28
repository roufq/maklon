# cPanel Cron Setup for Laravel Scheduler and Queue

Use these snippets in cPanel > Cron Jobs. Replace PATHS (user and PHP version) to match your hosting.

## 1) Determine PHP Binary

Common paths on cPanel:
- `/opt/cpanel/ea-php82/root/usr/bin/php`
- `/opt/cpanel/ea-php81/root/usr/bin/php`
- `/usr/local/bin/php`

Verify with:
```
which php
php -v
```

## 2) Scheduler (required)

Run every minute to trigger your scheduled tasks (weekly reports, stakeholder reminders):
```
* * * * * cd /home/USER/public_html/manajemen && /opt/cpanel/ea-php82/root/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

## 3) Queue Worker (optional but recommended in production)

If you switch to an async queue driver (e.g., `QUEUE_CONNECTION=database`), add this cron to process jobs every minute and exit when empty (safe for shared hosting):
```
* * * * * cd /home/USER/public_html/manajemen && /opt/cpanel/ea-php82/root/usr/bin/php artisan queue:work --stop-when-empty --tries=3 --timeout=60 >> storage/logs/queue.log 2>&1
```

Notes:
- Keep `--stop-when-empty` so each run exits. cPanel will re-run it every minute.
- For higher throughput, add a second line for a different queue name, or shorten the cron interval.

## 4) Environment Settings

In `.env` for production:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `QUEUE_CONNECTION=database` (or `redis` if available)
- `MAIL_MAILER=smtp` and SMTP credentials

Cache for performance:
```
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 5) Quick Verification

- List schedule: `php artisan schedule:list`
- Force run once: `php artisan schedule:run`
- Create a weekly report job manually: `php artisan reports:send-weekly`
- Check job tables: `jobs`, `failed_jobs`
- Check logs: `storage/logs/laravel.log`, `storage/logs/queue.log`

## 6) Alternative: Helper Scripts (optional)

If you prefer, point cron to these helpers (already in repo):
```
* * * * * bash /home/USER/public_html/manajemen/scripts/cron/schedule-run.sh
* * * * * bash /home/USER/public_html/manajemen/scripts/cron/queue-work.sh
```

