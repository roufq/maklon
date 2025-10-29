Rollback Plan

1) Enable maintenance mode: `php artisan down`
2) Restore previous code release (symlink swap or git checkout)
3) Rollback DB if needed: `php artisan migrate:rollback --step=1`
4) Clear caches: `php artisan optimize:clear`
5) Disable maintenance mode: `php artisan up`

Always take DB backup before major migrations (see scripts/backup/db-backup.sh).

