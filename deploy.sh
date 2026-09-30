#!/bin/bash
# ============================================================
#  PEW Training Center — Local Deployment Script
#  Deploys directly to server via FTP (no GitHub Actions required)
#  Use ./deploy.sh --check for a read-only FTP comparison.
#  Use ./deploy.sh --sync-env to upload/refresh remote .env from .env.production.
# ============================================================

set -e

DEPLOY_STARTED_AT=$(date +%s)
DEPLOY_STARTED_DISPLAY=$(date '+%Y-%m-%d %H:%M:%S %Z')

# Prevent concurrent deployments from interleaving FTP mirrors
DEPLOY_LOCK_PATH="/tmp/pewtc-deploy.lock"
exec 9>"$DEPLOY_LOCK_PATH"
if ! flock -n 9; then
    echo "⚠️ Previous deployment or lock detected. Cleaning up stale process..."
    LOCK_PIDS=$(fuser "$DEPLOY_LOCK_PATH" 2>/dev/null || true)
    OTHER_PIDS=$(pgrep -f "pewtc-deploy|deploy.sh|lftp" 2>/dev/null || true)
    PIDS_TO_KILL=$(echo "$LOCK_PIDS $OTHER_PIDS" | tr ' ' '\n' | grep -v "^$$$" | sort -u || true)

    if [ -n "$PIDS_TO_KILL" ]; then
        echo "$PIDS_TO_KILL" | xargs kill -9 2>/dev/null || true
        sleep 1
    fi

    exec 9>"$DEPLOY_LOCK_PATH"
    flock -n 9 || true
fi

CHECK_ONLY=false
SYNC_ENV=false

# Parse flags
while [[ $# -gt 0 ]]; do
    case "$1" in
        --check)
            CHECK_ONLY=true
            shift
            ;;
        --sync-env)
            SYNC_ENV=true
            shift
            ;;
        *)
            COMMIT_MSG="$1"
            shift
            ;;
    esac
done

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_deployment_timing() {
    local finished_at finished_display elapsed_seconds elapsed_minutes elapsed_remainder
    finished_at=$(date +%s)
    finished_display=$(date '+%Y-%m-%d %H:%M:%S %Z')
    elapsed_seconds=$((finished_at - DEPLOY_STARTED_AT))
    elapsed_minutes=$((elapsed_seconds / 60))
    elapsed_remainder=$((elapsed_seconds % 60))

    echo -e "  Deployment Started:  ${DEPLOY_STARTED_DISPLAY}"
    echo -e "  Hard refresh (Ctrl+Shift+R) to see changes."
    echo -e "  Deployment Finished: ${finished_display}"
    printf '  Estimated Time Taken: %02d min %02d sec\n' "$elapsed_minutes" "$elapsed_remainder"
}

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
LOCAL_ROOT="$SCRIPT_DIR"

# Load local .env if present
if [ -f "$SCRIPT_DIR/.env" ]; then
    while IFS= read -r line || [ -n "$line" ]; do
        line=$(echo "$line" | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//')
        if [[ -n "$line" && ! "$line" =~ ^# && "$line" =~ = ]]; then
            key="${line%%=*}"
            val="${line#*=}"
            val="${val#\"}"
            val="${val%\"}"
            val="${val#\'}"
            val="${val%\'}"
            export "$key=$val"
        fi
    done < "$SCRIPT_DIR/.env"
fi

# Credentials
FTP_HOST="${FTP_HOST:-ftp.pewtc.com}"
FTP_USER="${FTP_USER:-ftpx2@pewtc.com}"
FTP_PASS="${FTP_PASS:-ftpx2@pewtc.com}"
FTP_PORT="${FTP_PORT:-21}"
FTP_TIMEOUT_SECONDS="${FTP_TIMEOUT_SECONDS:-1800}"
SITE_URL="${SITE_URL:-https://pewtc.com}"
SITE_URL="${SITE_URL%/}"

if [ -z "$FTP_HOST" ] || [ -z "$FTP_USER" ] || [ -z "$FTP_PASS" ]; then
    echo -e "${RED}❌ FTP config missing in .env${NC}"
    echo "Add these to .env:"
    echo "  FTP_HOST=ftp.pewtc.com"
    echo "  FTP_USER=ftpx2@pewtc.com"
    echo "  FTP_PASS=your-password"
    echo "  FTP_PORT=21"
    exit 1
fi

case "$SITE_URL" in
    http://localhost*|https://localhost*|http://127.0.0.1*|https://127.0.0.1*)
        echo -e "${RED}❌ SITE_URL points to a local loopback address; refusing a production deployment${NC}"
        echo "Set SITE_URL to the deployed HTTPS hostname in .env (e.g. https://pewtc.com)."
        exit 1
        ;;
esac

MIRROR_MODE=""
if [ "$CHECK_ONLY" = true ]; then
    MIRROR_MODE="--dry-run"
fi

echo -e "${BLUE}============================================================${NC}"
echo -e "${BLUE}  PEW Training Center — Deployment${NC}"
echo -e "${BLUE}============================================================${NC}"
echo ""
echo -e "Deployment Started: $(date '+%Y-%m-%d %H:%M:%S %Z')"
echo -e "Server:    $FTP_HOST"
echo -e "Local:     $LOCAL_ROOT"
echo -e "Site URL:  $SITE_URL"
if [ "$CHECK_ONLY" = true ]; then
    echo -e "Mode:      read-only FTP preflight (--check)"
fi
echo ""

# ============================================================
# Step 1: Pre-flight validation
# ============================================================
echo -e "${YELLOW}▶ Step 1: Running pre-flight checks...${NC}"

# Check critical local files
REQUIRED_FILES=(
    "index.php"
    "wp-config.php"
    "wp-load.php"
    "migration_runner.php"
    "wp-content/themes/dist-faithful/style.css"
    "wp-content/plugins/pew-site-core/pew-site-core.php"
)

for rf in "${REQUIRED_FILES[@]}"; do
    if [ ! -f "$LOCAL_ROOT/$rf" ]; then
        echo -e "${RED}❌ Required file missing: $rf${NC}"
        exit 1
    fi
done

# Lint critical PHP files
echo -n "  PHP syntax check... "
php -l "$LOCAL_ROOT/wp-config.php" >/dev/null
php -l "$LOCAL_ROOT/migration_runner.php" >/dev/null
echo -e "${GREEN}OK${NC}"

# Auto-detect SERVER_ROOT based on ftp_current_dir.txt
# Rule: If ftp_current_dir.txt is in FTP root, that directory is public_html (SERVER_ROOT=".")
SERVER_ROOT="."
echo -e "  Remote target directory: ${SERVER_ROOT} (verified via ftp_current_dir.txt)"
echo -e "${GREEN}  ✅ Pre-flight checks passed${NC}"
echo ""

# ============================================================
# Step 2: Upload changed files via FTP (incremental sync)
# ============================================================
echo -e "${YELLOW}▶ Step 2: Uploading changed files to server...${NC}"
echo -e "  Only files with different sizes or timestamps are transferred (incremental sync)."
echo ""

LFTP_SCRIPT=$(mktemp /tmp/pewtc_deploy_XXXXXX.lftp)
trap 'rm -f "$LFTP_SCRIPT"' EXIT

cat > "$LFTP_SCRIPT" << LFTP_EOF
set ftp:ssl-allow no
set net:timeout 30
set net:max-retries 3
set ftp:passive-mode yes
set mirror:parallel-directories yes
set net:connection-limit 6
open ftp://$FTP_USER:$FTP_PASS@$FTP_HOST:$FTP_PORT
LFTP_EOF

# Upload .htaccess and .env.production
if [ "$CHECK_ONLY" = false ]; then
    if [ -f "$LOCAL_ROOT/.htaccess" ]; then
        cat >> "$LFTP_SCRIPT" << LFTP_EOF
echo "  📄 Uploading .htaccess..."
put "$LOCAL_ROOT/.htaccess" -o "$SERVER_ROOT/.htaccess"
LFTP_EOF
    fi

    if [ -f "$LOCAL_ROOT/.env.production" ]; then
        cat >> "$LFTP_SCRIPT" << LFTP_EOF
echo "  📄 Uploading remote environment config (.env)..."
put "$LOCAL_ROOT/.env.production" -o "$SERVER_ROOT/.env"
LFTP_EOF
    fi
fi

# Top-level directories to sync
SYNC_DIRS=("database" "scripts" "wp-admin" "wp-includes" "wp-content")

# 1. Sync root files first
cat >> "$LFTP_SCRIPT" << LFTP_EOF
echo "FOLDER: root"
mirror --no-recursion --reverse --verbose --no-perms --ignore-time --only-newer $MIRROR_MODE \
  --exclude-glob '.env*' --exclude-glob '*.sqlite*' --exclude-glob '*.dump' --exclude-glob '*.log' --exclude-glob '*.lock' --exclude-glob '*.md' --exclude-glob 'test_*.php' --exclude-glob 'verify_*.php' --exclude-glob '*-check.*' --exclude-glob 'docker-compose.yml' --exclude-glob 'license.txt' --exclude-glob 'readme.html' \
  "$LOCAL_ROOT/" "$SERVER_ROOT/"
LFTP_EOF

# 2. Sync main directories with recursion
for sdir in "${SYNC_DIRS[@]}"; do
    if [ -d "$LOCAL_ROOT/$sdir" ]; then
        cat >> "$LFTP_SCRIPT" << LFTP_EOF
echo "FOLDER: $sdir"
mirror --reverse --verbose --no-perms --ignore-time --only-newer $MIRROR_MODE \
  --exclude-glob '.env*' --exclude-glob '*.sqlite*' --exclude-glob '*.dump' --exclude-glob '*.log' --exclude-glob '*.lock' --exclude-glob 'cache/' --exclude-glob 'upgrade/' \
  "$LOCAL_ROOT/$sdir/" "$SERVER_ROOT/$sdir/"
LFTP_EOF
    fi
done

# Verification list of critical server files
cat >> "$LFTP_SCRIPT" << LFTP_EOF
ls -l $SERVER_ROOT/index.php
ls -l $SERVER_ROOT/wp-config.php
ls -l $SERVER_ROOT/migration_runner.php
ls -l $SERVER_ROOT/wp-content/plugins/pew-site-core/pew-site-core.php
ls -l $SERVER_ROOT/wp-content/themes/dist-faithful/style.css
quit
LFTP_EOF

LFTP_EXIT_FILE=$(mktemp /tmp/pewtc_lftp_exit_XXXXXX)
echo "0" > "$LFTP_EXIT_FILE"
set +e
(
  timeout --signal=TERM --kill-after=15s "${FTP_TIMEOUT_SECONDS}s" \
    lftp -f "$LFTP_SCRIPT" 2>&1
  echo $? > "$LFTP_EXIT_FILE"
) | tr '\r' '\n' | awk '
  /^FOLDER:/ { 
    folder = $0; sub(/^FOLDER: /, "", folder);
    print "  📁 Checking folder: " folder; fflush(); next 
  }
  /Transferring file/ { 
    f = $0; sub(/.*Transferring file ./, "", f); sub(/.$/, "", f);
    print "  📄 Uploaded: " f; fflush(); next 
  }
  /Removing old file/ { 
    f = $0; sub(/.*Removing old file ./, "", f); sub(/.$/, "", f);
    print "  🗑️  Deleted:   " f; fflush(); next 
  }
  /Making directory/ {
    f = $0; sub(/.*Making directory ./, "", f); sub(/.$/, "", f);
    print "  📁 Created:  " f; fflush(); next
  }
  /^-rw/ { print "  ✅ " $0; fflush(); next }
'
set -e
LFTP_EXIT=$(cat "$LFTP_EXIT_FILE")
rm -f "$LFTP_EXIT_FILE" "$LFTP_SCRIPT"

if [ "$LFTP_EXIT" -ne 0 ]; then
    echo -e "${RED}❌ FTP upload failed or timed out (exit code $LFTP_EXIT).${NC}"
    exit 1
fi

echo ""
if [ "$CHECK_ONLY" = true ]; then
    echo -e "${GREEN}  ✅ FTP comparison complete (no files modified)${NC}"
    print_deployment_timing
    exit 0
else
    echo -e "${GREEN}  ✅ Files uploaded successfully${NC}"
fi
echo ""

# ============================================================
# Step 3: Verification
# ============================================================
echo -e "${YELLOW}▶ Step 3: Verifying deployment...${NC}"
echo -e "  Server:   $FTP_HOST"
echo -e "  Site URL: $SITE_URL"
echo ""

# ============================================================
# Step 4: Run pending production migrations
# ============================================================
echo -e "${YELLOW}▶ Step 4: Running pending production migrations...${NC}"
echo ""

# Note: curl -k is used because the server uses a self-signed certificate
curl -fsS -k --retry 2 --retry-delay 2 --max-time 120 \
  -X POST \
  "$SITE_URL/migration_runner.php"
echo ""
echo -e "${GREEN}  ✅ Migrations complete${NC}"
echo ""

# ============================================================
# Step 4b: Live smoke checks
# ============================================================
echo -e "${YELLOW}▶ Step 4b: Running post-migration live smoke checks...${NC}"
SITE_URL="$SITE_URL" bash "$SCRIPT_DIR/scripts/live-smoke.sh"
echo ""

# ============================================================
# Step 5: Git commit & push (if dirty and not --check)
# ============================================================
echo -e "${YELLOW}▶ Step 5: Committing and pushing to git...${NC}"
echo ""
DEFAULT_MSG="Deploy PEW Training Center: $(date '+%Y-%m-%d %H:%M:%S')"
COMMIT_MSG="${COMMIT_MSG:-$DEFAULT_MSG}"

if [ -n "$(git status --porcelain)" ]; then
    echo -e "  Staging changes..."
    git add .
    echo -e "  Committing: '$COMMIT_MSG'"
    git commit -m "$COMMIT_MSG"
    echo -e "  Pushing to remote repository..."
    git push origin main
    echo -e "${GREEN}  ✅ Git commit & push complete${NC}"
else
    echo -e "  No uncommitted changes detected. Verifying remote status..."
    git push origin main || true
    echo -e "${GREEN}  ✅ Git status clean & up-to-date${NC}"
fi
echo ""

# ============================================================
# Summary
# ============================================================
echo -e "${BLUE}============================================================${NC}"
echo -e "${GREEN}  ✅ Deployment, Migrations & Verification complete!${NC}"
echo -e "${BLUE}============================================================${NC}"
echo ""
print_deployment_timing
echo ""
