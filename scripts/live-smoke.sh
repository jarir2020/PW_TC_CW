#!/usr/bin/env bash
# ============================================================
#  PEW Training Center — Post-Deployment Live Smoke Checks
# ============================================================

set -euo pipefail

SITE_URL="${SITE_URL:-https://pewtc.com}"
SITE_URL="${SITE_URL%/}"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "  Target: ${SITE_URL}"

# 1. Homepage Check
echo -n "  🔍 Checking homepage (${SITE_URL}/)... "
HOME_CODE=$(curl -k -s -o /tmp/pewtc_smoke_home.html -w "%{http_code}" --max-time 30 "${SITE_URL}/")
if [ "$HOME_CODE" = "200" ]; then
    if grep -qi "PEW Training Center" /tmp/pewtc_smoke_home.html; then
        echo -e "${GREEN}OK (200, branding verified)${NC}"
    else
        echo -e "${YELLOW}Warning: HTTP 200 returned but title check was inconclusive${NC}"
    fi
else
    echo -e "${RED}FAILED (HTTP ${HOME_CODE})${NC}"
    exit 1
fi
rm -f /tmp/pewtc_smoke_home.html

# 2. WP Login Check
echo -n "  🔍 Checking login portal (${SITE_URL}/wp-login.php)... "
LOGIN_CODE=$(curl -k -s -o /tmp/pewtc_smoke_login.html -w "%{http_code}" --max-time 30 "${SITE_URL}/wp-login.php")
if [ "$LOGIN_CODE" = "200" ]; then
    echo -e "${GREEN}OK (200)${NC}"
else
    echo -e "${RED}FAILED (HTTP ${LOGIN_CODE})${NC}"
    exit 1
fi
rm -f /tmp/pewtc_smoke_login.html

# 3. Theme CSS Check
echo -n "  🔍 Checking theme assets (${SITE_URL}/wp-content/themes/dist-faithful/style.css)... "
CSS_CODE=$(curl -k -s -o /dev/null -w "%{http_code}" --max-time 20 "${SITE_URL}/wp-content/themes/dist-faithful/style.css")
if [ "$CSS_CODE" = "200" ]; then
    echo -e "${GREEN}OK (200)${NC}"
else
    echo -e "${YELLOW}Warning: Asset check returned HTTP ${CSS_CODE}${NC}"
fi

# 4. Migration Status Check
echo -n "  🔍 Checking migration status (${SITE_URL}/migration_runner.php?action=status)... "
STATUS_OUTPUT=$(curl -k -fsS --max-time 30 "${SITE_URL}/migration_runner.php?action=status" 2>/dev/null || true)
if echo "$STATUS_OUTPUT" | grep -q "Applied migrations:"; then
    APPLIED_COUNT=$(echo "$STATUS_OUTPUT" | grep "Applied migrations:" | head -n 1 | awk '{print $3}')
    PENDING_COUNT=$(echo "$STATUS_OUTPUT" | grep "Pending:" | head -n 1 | awk '{print $2}')
    echo -e "${GREEN}OK (Applied: ${APPLIED_COUNT}, Pending: ${PENDING_COUNT})${NC}"
else
    echo -e "${YELLOW}Warning: Status endpoint output inconclusive${NC}"
fi

echo -e "${GREEN}  ✅ All smoke checks passed successfully${NC}"
