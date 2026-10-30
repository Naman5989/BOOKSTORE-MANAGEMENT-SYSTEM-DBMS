#!/bin/bash
# =====================================================
# run.sh — one-step setup + launch for the Online Bookstore
#
# Prerequisites (one-time, must already be installed):
#   - MySQL server running locally
#   - PHP installed (php -v works)
#
# Usage:
#   ./run.sh                 (uses root user, no password, port 8000)
#   ./run.sh myuser mypass    (custom MySQL user/password)
# =====================================================

set -e

DB_USER="${1:-root}"
DB_PASS="${2:-}"
PORT="${3:-8000}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "Step 1/2: Creating database and loading schema..."
for f in 01_database.sql 02_ddl.sql 03_dml.sql 04_dcl.sql 05_trigger.sql 06_procedure.sql; do
    echo "  -> $f"
    if [ -z "$DB_PASS" ]; then
        mysql -u "$DB_USER" < "$SCRIPT_DIR/database/$f"
    else
        mysql -u "$DB_USER" -p"$DB_PASS" < "$SCRIPT_DIR/database/$f"
    fi
done

echo "Step 2/2: Starting the site at http://localhost:$PORT/index.php"
echo "(Press Ctrl+C to stop the server)"
cd "$SCRIPT_DIR/frontend"
php -S "localhost:$PORT"
