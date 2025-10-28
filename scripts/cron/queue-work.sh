#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/../.."
PHP_BIN="${PHP_BIN:-/opt/cpanel/ea-php82/root/usr/bin/php}"
exec "$PHP_BIN" artisan queue:work --stop-when-empty --tries=3 --timeout=60 >> storage/logs/queue.log 2>&1

