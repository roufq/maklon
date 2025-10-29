#!/usr/bin/env bash
set -euo pipefail

DEST_DIR=${1:-"./backups"}
STAMP=${2:-$(date +"%Y%m%d_%H%M%S")}
mkdir -p "$DEST_DIR"

case "${DB_CONNECTION:-mysql}" in
  mysql|mariadb)
    : "${DB_HOST:?DB_HOST not set}"
    : "${DB_PORT:?DB_PORT not set}"
    : "${DB_DATABASE:?DB_DATABASE not set}"
    : "${DB_USERNAME:?DB_USERNAME not set}"
    : "${DB_PASSWORD:?DB_PASSWORD not set}"
    mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" --routines --single-transaction "$DB_DATABASE" \
      | gzip > "$DEST_DIR/${DB_DATABASE}_${STAMP}.sql.gz"
    ;;
  pgsql|postgres)
    : "${DB_HOST:?DB_HOST not set}"
    : "${DB_PORT:?DB_PORT not set}"
    : "${DB_DATABASE:?DB_DATABASE not set}"
    : "${DB_USERNAME:?DB_USERNAME not set}"
    PGPASSWORD="${DB_PASSWORD:-}" pg_dump -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE" | gzip > "$DEST_DIR/${DB_DATABASE}_${STAMP}.sql.gz"
    ;;
  *)
    echo "Unsupported DB_CONNECTION: ${DB_CONNECTION:-}"
    exit 1
    ;;
esac

echo "Backup created at $DEST_DIR"

