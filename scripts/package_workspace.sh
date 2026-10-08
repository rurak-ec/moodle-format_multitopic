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
if command -v zip >/dev/null 2>&1; then
  cp -a "${SRC_DIR}" "${STAGE_DIR}/multitopic"
  (
    cd "${STAGE_DIR}"
    zip -rq "${OUT_ZIP}" "multitopic" -x '*.DS_Store' '*__MACOSX*' '*/.git/*'
  )
elif command -v python3 >/dev/null 2>&1; then
  python3 -c "
import sys, os, zipfile
src = sys.argv[1]
out = sys.argv[2]
with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED) as z:
    for root, dirs, files in os.walk(src):
        for f in files:
            if f in ('.DS_Store',) or '__MACOSX' in root or '/.git' in root:
                continue
            full = os.path.join(root, f)
            rel = os.path.relpath(full, src)
            z.write(full, os.path.join('multitopic', rel))
" "${SRC_DIR}" "${OUT_ZIP}"
elif command -v docker >/dev/null 2>&1; then
  # The development host intentionally has no zip binary. Reuse the project's
  # PHP image, whose ZipArchive extension is already required by Moodle.
  case "${SRC_DIR}" in
    "${ROOT_DIR}"/*) RELATIVE_SRC="${SRC_DIR#"${ROOT_DIR}"/}" ;;
    *)
      echo "ERROR: Docker fallback only supports a source inside ${ROOT_DIR}" >&2
      exit 1
      ;;
  esac

  docker run --rm --user "$(id -u):$(id -g)" --entrypoint php \
    -v "${ROOT_DIR}:/workspace" \
    moodle:5.2.1-pgsql \
    -r '$source = "/workspace/" . $argv[1]; $output = "/workspace/" . $argv[2]; $zip = new ZipArchive(); if ($zip->open($output, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) { fwrite(STDERR, "Unable to create ZIP\\n"); exit(1); } $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS)); foreach ($iterator as $file) { $relative = substr($file->getPathname(), strlen($source) + 1); if (!$file->isFile() || preg_match("#(^|/)(\\.git|__MACOSX)/|\\.DS_Store$#", $relative)) { continue; } $zip->addFile($file->getPathname(), "multitopic/" . $relative); } $zip->close();' \
    "${RELATIVE_SRC}" "build/$(basename "${OUT_ZIP}")"
else
  echo "ERROR: zip is not installed and Docker is unavailable for the fallback." >&2
  exit 1
fi

echo "OK: package created"
echo "  ${OUT_ZIP}"
