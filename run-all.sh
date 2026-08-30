#!/usr/bin/env bash
#
# Boot every demo at once, each one on its own port.
#
# Usage:  ./run-all.sh
#
# The pure PHP demos are served by the PHP built-in server, the Symfony
# application by the same server on top of public/index.php. Dependencies are
# installed on first run. Ctrl+C stops everything.

set -euo pipefail

cd "$(dirname "$0")"

DEMOS=(
    "basic-demo:8101:Username based registration and login"
    "usernameless-demo:8102:Discoverable credentials and Conditional UI"
    "passkey-upgrade-demo:8103:Silent enrollment after a password login"
    "signal-api-demo:8104:The three W3C Signal methods"
    "extensions-demo:8105:credProps, credProtect, credBlob, minPinLength"
    "prf-demo:8106:PRF extension and client side encryption"
    "spc-demo:8107:Secure Payment Confirmation (same-origin flavour)"
)
SYMFONY_PORT=8110

PIDS=()

cleanup() {
    echo
    echo "Stopping every demo..."
    for pid in "${PIDS[@]:-}"; do
        [[ -n "$pid" ]] && kill "$pid" 2>/dev/null || true
    done
    wait 2>/dev/null || true
}
trap cleanup INT TERM EXIT

is_busy() { lsof -i ":$1" >/dev/null 2>&1 || ss -ltn 2>/dev/null | grep -q ":$1 "; }

for entry in "${DEMOS[@]}" "symfony-demo:$SYMFONY_PORT:Full Symfony application"; do
    port="${entry#*:}"
    port="${port%%:*}"
    if is_busy "$port"; then
        echo "Port $port is already in use. Free it and retry." >&2
        exit 1
    fi
done

for entry in "${DEMOS[@]}"; do
    demo="${entry%%:*}"
    rest="${entry#*:}"
    port="${rest%%:*}"

    if [[ ! -d "$demo/vendor" ]]; then
        echo "Installing the dependencies of $demo..."
        (cd "$demo" && composer install --no-interaction --quiet)
    fi

    mkdir -p "$demo/var"
    php -S "localhost:$port" -t "$demo/same-origin/public" "$demo/same-origin/router.php" \
        >"$demo/var/server.log" 2>&1 &
    PIDS+=("$!")
done

if [[ ! -d symfony-demo/vendor ]]; then
    echo "Installing the dependencies of symfony-demo..."
    (cd symfony-demo && composer install --no-interaction --quiet)
fi
if [[ ! -f symfony-demo/var/tailwind/app.built.css ]]; then
    echo "Building the Tailwind stylesheet of symfony-demo..."
    (cd symfony-demo && php bin/console tailwind:build --minify)
fi
if [[ ! -f symfony-demo/.env.local ]]; then
    echo "Pointing symfony-demo at a local SQLite database (symfony-demo/.env.local)..."
    printf 'DATABASE_URL="sqlite:///%%kernel.project_dir%%/var/app.db"\n' > symfony-demo/.env.local
fi
if [[ ! -f symfony-demo/var/app.db ]] && grep -q 'sqlite' symfony-demo/.env.local; then
    echo "Creating the schema of symfony-demo..."
    (cd symfony-demo && php bin/console doctrine:schema:create --quiet)
fi
(cd symfony-demo && mkdir -p var && APP_ENV=dev php -S "localhost:$SYMFONY_PORT" -t public public/index.php \
    >var/server.log 2>&1) &
PIDS+=("$!")

echo
echo "Every demo is running:"
echo
for entry in "${DEMOS[@]}"; do
    demo="${entry%%:*}"
    rest="${entry#*:}"
    port="${rest%%:*}"
    label="${rest#*:}"
    printf '  http://localhost:%-6s %-22s %s\n' "$port" "$demo" "$label"
done
printf '  http://localhost:%-6s %-22s %s\n' "$SYMFONY_PORT" "symfony-demo" "Full Symfony application"
echo
echo "The Symfony application runs on plain HTTP here, which localhost makes a"
echo "secure context, and on a SQLite database created on first run. Its Docker"
echo "setup keeps HTTPS and PostgreSQL."
echo
echo "Ctrl+C stops everything."

wait
