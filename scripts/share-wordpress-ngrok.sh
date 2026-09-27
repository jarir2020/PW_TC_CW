#!/usr/bin/env bash
set -euo pipefail

# Backward-compatible filename. The implementation is token-free and uses
# Cloudflare Quick Tunnels because current ngrok agents require authentication.
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
exec bash "$SCRIPT_DIR/share-wordpress-quick-tunnel.sh" "$@"
