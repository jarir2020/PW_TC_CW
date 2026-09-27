#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$ROOT_DIR/.env"
HOST="${HOST:-127.0.0.1}"
SERVER_PID=""
SERVER_LOG=""
TUNNEL_PID=""
TUNNEL_LOG=""

usage() {
  cat <<'HELP'
Usage: bash scripts/share-wordpress-quick-tunnel.sh [options]

Open the local WordPress site through a temporary, token-free Cloudflare Quick Tunnel.

Options:
  --port PORT       Local WordPress port (default: PORT or WORDPRESS_PORT from .env)
  --no-start        Do not start PHP when the selected port is unused
  --help            Show this help

Examples:
  bash scripts/share-wordpress-quick-tunnel.sh
  bash scripts/share-wordpress-quick-tunnel.sh --port 8090
HELP
}

START_SERVER=1
REQUESTED_PORT=""
while [[ $# -gt 0 ]]; do
  case "$1" in
    --port)
      [[ $# -ge 2 ]] || { printf '%s\n' '--port requires a value.' >&2; exit 2; }
      REQUESTED_PORT="$2"
      shift 2
      ;;
    --no-start)
      START_SERVER=0
      shift
      ;;
    --help|-h)
      usage
      exit 0
      ;;
    *)
      printf 'Unknown option: %s\n' "$1" >&2
      usage >&2
      exit 2
      ;;
  esac
done

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

CLOUDFLARED_BIN="${CLOUDFLARED_BIN:-cloudflared}"
if ! command -v "$CLOUDFLARED_BIN" >/dev/null 2>&1; then
  printf '%s\n' 'cloudflared was not found. Install it from https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/downloads/' >&2
  exit 1
fi

if ! command -v php >/dev/null 2>&1; then
  printf '%s\n' 'php is required to start the local WordPress server.' >&2
  exit 1
fi

if ! command -v curl >/dev/null 2>&1; then
  printf '%s\n' 'curl is required to check the local WordPress server.' >&2
  exit 1
fi

if ! command -v grep >/dev/null 2>&1; then
  printf '%s\n' 'grep is required to detect the temporary public URL.' >&2
  exit 1
fi

PORT="${REQUESTED_PORT:-${PORT:-${WORDPRESS_PORT:-8080}}}"
if [[ ! "$PORT" =~ ^[0-9]+$ || "$PORT" -lt 1 || "$PORT" -gt 65535 ]]; then
  printf 'Invalid local port: %s\n' "$PORT" >&2
  exit 2
fi

port_is_busy() {
  if command -v ss >/dev/null 2>&1; then
    ss -ltn "( sport = :$1 )" 2>/dev/null | awk 'NR > 1 { found = 1 } END { exit !found }'
    return
  fi
  curl -sS --connect-timeout 1 -o /dev/null "http://$HOST:$1/" >/dev/null 2>&1
}

cleanup() {
  if [[ -n "$TUNNEL_PID" ]] && kill -0 "$TUNNEL_PID" 2>/dev/null; then
    kill -TERM "$TUNNEL_PID" 2>/dev/null || true
    wait "$TUNNEL_PID" 2>/dev/null || true
  fi
  if [[ -n "$SERVER_PID" ]] && kill -0 "$SERVER_PID" 2>/dev/null; then
    kill -TERM "$SERVER_PID" 2>/dev/null || true
    wait "$SERVER_PID" 2>/dev/null || true
  fi
  if [[ -n "$TUNNEL_LOG" && -f "$TUNNEL_LOG" ]]; then
    rm -f "$TUNNEL_LOG"
  fi
  if [[ -n "$SERVER_LOG" && -f "$SERVER_LOG" ]]; then
    rm -f "$SERVER_LOG"
  fi
}
trap cleanup EXIT INT TERM

if port_is_busy "$PORT"; then
  printf 'Using the existing local WordPress server on http://%s:%s/\n' "$HOST" "$PORT"
else
  if [[ "$START_SERVER" -eq 0 ]]; then
    printf 'No local server is listening on port %s and --no-start was supplied.\n' "$PORT" >&2
    exit 1
  fi

  SERVER_LOG="$(mktemp "${TMPDIR:-/tmp}/pew-wordpress-quick-tunnel.XXXXXX.log")"
  (
    cd "$ROOT_DIR"
    export WP_HOME="${WP_HOME:-http://$HOST:$PORT}"
    export WP_SITEURL="${WP_SITEURL:-$WP_HOME}"
    php -S "$HOST:$PORT" -t "$ROOT_DIR" "$ROOT_DIR/scripts/wordpress-router.php"
  ) >"$SERVER_LOG" 2>&1 &
  SERVER_PID=$!

  for _ in {1..30}; do
    if curl -sS --connect-timeout 1 -o /dev/null "http://$HOST:$PORT/"; then
      break
    fi
    if ! kill -0 "$SERVER_PID" 2>/dev/null; then
      printf 'The local WordPress server exited. Log:\n%s\n' "$SERVER_LOG" >&2
      sed -n '1,80p' "$SERVER_LOG" >&2
      exit 1
    fi
    sleep 1
  done

  if ! curl -sS --connect-timeout 2 -o /dev/null "http://$HOST:$PORT/"; then
    printf 'The local WordPress server did not become ready on port %s.\n' "$PORT" >&2
    sed -n '1,80p' "$SERVER_LOG" >&2
    exit 1
  fi
  printf 'Started local WordPress server on http://%s:%s/\n' "$HOST" "$PORT"
fi

TUNNEL_LOG="$(mktemp "${TMPDIR:-/tmp}/pew-cloudflare-tunnel.XXXXXX.log")"
printf '%s\n' 'Starting a temporary token-free Cloudflare Quick Tunnel...'
"$CLOUDFLARED_BIN" tunnel --url "http://$HOST:$PORT" >"$TUNNEL_LOG" 2>&1 &
TUNNEL_PID=$!

PUBLIC_URL=""
for _ in {1..60}; do
  PUBLIC_URL="$(grep -oE 'https://[[:alnum:]][[:alnum:].-]*\.trycloudflare\.com' "$TUNNEL_LOG" | head -n 1 || true)"
  if [[ -n "$PUBLIC_URL" ]]; then
    break
  fi
  if ! kill -0 "$TUNNEL_PID" 2>/dev/null; then
    printf '%s\n' 'Cloudflare Quick Tunnel exited before producing a public URL.' >&2
    sed -n '1,120p' "$TUNNEL_LOG" >&2
    exit 1
  fi
  sleep 0.5
done

if [[ -z "$PUBLIC_URL" ]]; then
  printf '%s\n' 'Timed out waiting for the Cloudflare public URL.' >&2
  sed -n '1,120p' "$TUNNEL_LOG" >&2
  exit 1
fi

printf '\n========================================\n'
printf 'PUBLIC URL: %s\n' "$PUBLIC_URL"
printf '========================================\n\n'
printf '%s\n' 'The tunnel is live. Press Ctrl+C to close it.'
wait "$TUNNEL_PID"
