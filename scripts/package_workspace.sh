#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SRC_DIR="${1:-${ROOT_DIR}/workspace/format_multitopic}"
BUILD_DIR="${ROOT_DIR}/build"
STAMP="$(date +%Y%m%d_%H%M%S)"
OUT_ZIP="${BUILD_DIR}/format_multitopic_${STAMP}.zip"
STAGE_DIR="$(mktemp -d)"

if [[ ! -d "${SRC_DIR}" ]]; then
  echo "ERROR: workspace plugin not found: ${SRC_DIR}" >&2
  echo "Expected the plugin source at workspace/format_multitopic." >&2
  exit 1
fi

mkdir -p "${BUILD_DIR}"

# NOTE: The AMD modules are ES6 (import/export) and are compiled to amd/build/
# with `grunt amd` (rollup). Do NOT copy amd/src over amd/build here -- the
# committed amd/build/*.min.js is the rollup output and must ship as-is.

cleanup() {
  rm -rf "${STAGE_DIR}"
}
trap cleanup EXIT

# Moodle expects the format plugin folder inside course/format/ to be "multitopic"
# (the plugin name without the "format_" type prefix), not "format_multitopic".
cp -a "${SRC_DIR}" "${STAGE_DIR}/multitopic"

(
  cd "${STAGE_DIR}"
  zip -rq "${OUT_ZIP}" "multitopic" -x '*.DS_Store' '*__MACOSX*' '*/.git/*'
)

echo "OK: package created"
echo "  ${OUT_ZIP}"
