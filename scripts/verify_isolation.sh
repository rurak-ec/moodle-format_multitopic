#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "Isolation check for: ${ROOT_DIR}"

for dir in workspace scripts docs; do
  if [[ ! -d "${ROOT_DIR}/${dir}" ]]; then
    echo "ERROR: missing directory ${ROOT_DIR}/${dir}" >&2
    exit 1
  fi
done

if [[ -d "${ROOT_DIR}/workspace/format_multitopic" ]]; then
  echo "OK: workspace/format_multitopic found"
else
  echo "WARN: workspace/format_multitopic not found"
fi

if [[ -f "${ROOT_DIR}/workspace/format_multitopic/version.php" ]]; then
  echo "OK: version.php present"
fi

echo "OK: base structure looks good for development."
