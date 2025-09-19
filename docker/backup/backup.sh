#!/usr/bin/env bash
set -euo pipefail

# ===== Time =====
TS="$(date -u +"%Y-%m-%dT%H-%M-%SZ")"
NAME="${PROJECT_NAME:-project}"

BACKUP_TELEGRAM_KEY="${BACKUP_TELEGRAM_BOT_KEY:-}"
BACKUP_TELEGRAM_CHAT="${BACKUP_TELEGRAM_CHAT_ID:-}"

API_HOST="${API_HOST:-telegram-bot-api:8081}"
API="http://${API_HOST}/bot${BACKUP_TELEGRAM_KEY}"

if [[ -z "$BACKUP_TELEGRAM_KEY" || -z "$BACKUP_TELEGRAM_CHAT" ]]; then
  echo "[backup] BACKUP_TELEGRAM_KEY or BACKUP_TELEGRAM_CHAT is empty. Exit."
  exit 0
fi

# ===== DB configs =====
DB_ENGINE="${DB_ENGINE:-mysql}"
DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_NAME="${DB_NAME:-app}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-root}"

# ===== Project files =====
PROJECT_PATH="${PROJECT_PATH:-/var/www/html}"
EXCLUDES="${EXCLUDES:-}"              # "vendor node_modules var/log .git docker/nginx/logs"

# ===== Helpers =====
send_doc () {
  local caption="$1" fname="$2"
  curl -sS -F "chat_id=${BACKUP_TELEGRAM_CHAT}" \
           -F "caption=${caption}" \
           -F "document=@-;filename=${fname}" \
           "${API}/sendDocument" >/dev/null
}

send_msg () {
  local text="$1"
  curl -sS -X POST -H "Content-Type: application/json" \
    -d "{\"chat_id\":\"${BACKUP_TELEGRAM_CHAT}\",\"text\":$(jq -Rn --arg t "$text" '$t'),\"disable_web_page_preview\":true}" \
    "${API}/sendMessage" >/dev/null
}

dump_db () {
  case "${DB_ENGINE}" in
    mysql)
      mysqldump \
        --single-transaction \
        --skip-lock-tables \
        --quick \
        --default-character-set=utf8mb4 \
        -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USER}" -p"${DB_PASS}" "${DB_NAME}"
      ;;
    pgsql)
      PGPASSWORD="${DB_PASS}" pg_dump \
        --no-owner --no-privileges \
        --host="${DB_HOST}" --port="${DB_PORT}" \
        --username="${DB_USER}" --dbname="${DB_NAME}"
      ;;
    *)
      echo "[backup] ERROR: unknown DB_ENGINE='${DB_ENGINE}', expected mysql|pgsql" >&2
      exit 1
      ;;
  esac
}

# ===== 1) Header message =====
# For example: "Api Starter Kit — 2025-09-18T05-46-00Z"
send_msg "${NAME} — ${TS}"

# ===== 2) Archive project files (.tar.gz) → Telegram =====
EXC_ARGS=()
for e in ${EXCLUDES}; do EXC_ARGS+=( "--exclude=${e}" ); done

echo "[backup] Archiving project files (.tar.gz) and streaming to Telegram..."
tar -C / "${EXC_ARGS[@]}" -czf - "${PROJECT_PATH#/}" \
| tee >(sha256sum | awk '{print $1}' > /tmp/files.sha256) \
| send_doc "📦 ${NAME} files ${TS}" "${NAME}_${TS}_files.tar.gz"

# ===== 3) DB Dump (.sql.gz) → Telegram =====
echo "[backup] Dumping database (${DB_ENGINE}) and streaming to Telegram..."
dump_db \
| gzip -c \
| tee >(sha256sum | awk '{print $1}' > /tmp/db.sha256) \
| send_doc "🗄️ ${NAME} db ${TS} (${DB_ENGINE})" "${NAME}_${TS}_db.sql.gz"

# ===== 4) Report =====
FILES_SHA="$(cat /tmp/files.sha256 2>/dev/null || echo '-')"
DB_SHA="$(cat /tmp/db.sha256 2>/dev/null || echo '-')"

echo "[backup] Done."
