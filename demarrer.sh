#!/usr/bin/env bash
# Démarre le site localement avec les limites nécessaires aux photos de 10 Mo.
set -e
cd -- "$(dirname -- "${BASH_SOURCE[0]}")"
if ! command -v php >/dev/null 2>&1; then
    echo "PHP 8.1 ou supérieur doit être installé pour lancer le site." >&2
    exit 1
fi
printf '%s\n' 'Site : http://localhost:8000 — arrêt : Ctrl + C'
exec php -d upload_max_filesize=10M -d post_max_size=16M -d memory_limit=256M -S localhost:8000 -t .
