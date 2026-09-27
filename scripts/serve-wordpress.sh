#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$ROOT_DIR/.env"
HOST="${HOST:-127.0.0.1}"

if [[ ! -f "$ROOT_DIR/index.php" || ! -f "$ROOT_DIR/wp-settings.php" ]]; then
  printf '%s\n' 'WordPress core is not installed yet. Run scripts/prepare-wordpress.sh first.' >&2
  exit 1
fi

if [[ -f "$ENV_FILE" ]]; then
  set -a
  # shellcheck disable=SC1090
  source "$ENV_FILE"
  set +a
fi

port_is_busy() {
  if ! command -v ss >/dev/null 2>&1; then
    return 1
  fi
  ss -ltn "( sport = :$1 )" 2>/dev/null | awk 'NR > 1 { found = 1 } END { exit !found }'
}

REQUESTED_PORT="${PORT:-${WORDPRESS_PORT:-8080}}"
PORT="$REQUESTED_PORT"

if port_is_busy "$PORT"; then
  for candidate in 8090 8091 8092 8093; do
    if ! port_is_busy "$candidate"; then
      PORT="$candidate"
      printf 'Port %s is busy; using port %s instead.\n' "$REQUESTED_PORT" "$PORT" >&2
      break
    fi
  done
fi

export WP_HOME="${WP_HOME:-http://$HOST:$PORT}"
export WP_SITEURL="${WP_SITEURL:-$WP_HOME}"

cd "$ROOT_DIR"
exec php -S "$HOST:$PORT" -t "$ROOT_DIR" "$ROOT_DIR/scripts/wordpress-router.php"

