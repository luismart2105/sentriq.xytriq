#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

readonly PROD_DB='zauryx_sentriq_prod'
readonly QA_DB='zauryx_sentriq_qa'
readonly MYSQL='/usr/bin/mariadb'
readonly DUMP='/usr/bin/mariadb-dump'
readonly LOG='/var/log/sentriq-qa-db-sync.log'

exec 9>/run/lock/sentriq-qa-db-sync.lock
/usr/bin/flock -n 9 || exit 0

tmp_dump=$(/usr/bin/mktemp /var/tmp/sentriq-qa-db.XXXXXX.sql)
cleanup() { /usr/bin/rm -f -- "$tmp_dump"; }
trap cleanup EXIT
trap 'status=$?; /usr/bin/printf "[%s] ERROR: sync failed (exit %s).\n" "$(/usr/bin/date --iso-8601=seconds)" "$status" >> "$LOG"; exit "$status"' ERR

/usr/bin/printf '[%s] Starting production-to-QA database refresh.\n' "$(/usr/bin/date --iso-8601=seconds)" >> "$LOG"

"$DUMP" --defaults-extra-file=/root/.my.cnf --single-transaction --quick --skip-lock-tables --hex-blob --triggers "$PROD_DB" > "$tmp_dump"
[[ -s "$tmp_dump" ]]

{
    /usr/bin/printf '%s\n' 'SET FOREIGN_KEY_CHECKS=0;'
    "$MYSQL" --defaults-extra-file=/root/.my.cnf --batch --skip-column-names --execute="SELECT CONCAT('DROP ', IF(TABLE_TYPE = 'VIEW', 'VIEW', 'TABLE'), ' IF EXISTS ', CHAR(96), REPLACE(TABLE_NAME, CHAR(96), CONCAT(CHAR(96), CHAR(96))), CHAR(96), ';') FROM information_schema.TABLES WHERE TABLE_SCHEMA = '${QA_DB}';"
    /usr/bin/printf '%s\n' 'SET FOREIGN_KEY_CHECKS=1;'
} | "$MYSQL" --defaults-extra-file=/root/.my.cnf "$QA_DB"

"$MYSQL" --defaults-extra-file=/root/.my.cnf "$QA_DB" < "$tmp_dump"
/usr/bin/printf '[%s] Refresh completed successfully.\n' "$(/usr/bin/date --iso-8601=seconds)" >> "$LOG"
