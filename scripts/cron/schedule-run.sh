#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/../.."
PHP_BIN="${PHP_BIN:-/opt/cpanel/ea-php82/root/usr/bin/php}"
exec "$PHP_BIN" artisan schedule:run >> /dev/null 2>&1

