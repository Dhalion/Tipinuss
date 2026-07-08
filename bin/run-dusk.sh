#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

CROSS_ENV=(
  -u SESSION_DRIVER -u SESSION_LIFETIME -u SESSION_ENCRYPT
  -u SESSION_SECURE_COOKIE -u SESSION_PATH -u SESSION_DOMAIN
  -u DB_CONNECTION -u DB_HOST -u DB_PORT -u DB_DATABASE
  -u DB_USERNAME -u DB_PASSWORD
  -u APP_ENV -u APP_DEBUG -u APP_URL -u APP_KEY
  -u CACHE_STORE -u CACHE_PREFIX
  -u QUEUE_CONNECTION -u BROADCAST_DRIVER -u BROADCAST_CONNECTION
  -u MAIL_MAILER -u LOG_CHANNEL -u LOG_LEVEL
)

SERVER_PATTERN='serve --host=127.0.0.1 --port=8000 --env=dusk'

cleanup() {
  pkill -f "$SERVER_PATTERN" 2>/dev/null || true
}
trap cleanup EXIT

cleanup
sleep 1

rm -f /tmp/tipinuss_dusk.sqlite
touch /tmp/tipinuss_dusk.sqlite
chmod 666 /tmp/tipinuss_dusk.sqlite
echo "[dusk] reset /tmp/tipinuss_dusk.sqlite"

env "${CROSS_ENV[@]}" APP_ENV=dusk APP_DEBUG=false \
  php artisan serve --host=127.0.0.1 --port=8000 --env=dusk \
  >/tmp/serve-dusk.log 2>&1 &
SERVER_PID=$!
echo "[dusk] serve pid=$SERVER_PID"

ready=0
for _ in $(seq 1 30); do
  if curl -sf http://127.0.0.1:8000/up >/dev/null 2>&1; then
    ready=1
    echo "[dusk] server ready"
    break
  fi
  sleep 1
done

if [[ $ready -eq 0 ]]; then
  echo "[dusk] server failed to become ready; last log:" >&2
  tail -n 30 /tmp/serve-dusk.log >&2 || true
  kill "$SERVER_PID" 2>/dev/null || true
  exit 1
fi

env "${CROSS_ENV[@]}" APP_ENV=dusk APP_DEBUG=false \
  php artisan dusk

EXIT=$?

kill "$SERVER_PID" 2>/dev/null || true
wait "$SERVER_PID" 2>/dev/null || true

exit $EXIT
