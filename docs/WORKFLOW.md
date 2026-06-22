# Development workflow

This repo follows the rurak-ec Moodle-plugin workspace convention.

## Layout
- `workspace/format_multitopic/` — the actual Moodle plugin source (**edit here**). When deployed into
  a Moodle site this directory lives at `{moodleroot}/course/format/multitopic/`.
- `scripts/` — `package_workspace.sh` (build installable ZIP) and `verify_isolation.sh` (structure check).
- `docs/` — this file and `PLUGIN_MAP.md`.
- `build/` — generated ZIP artifacts (git-ignored, not plugin source).
- `translations/` — language packs kept for AMOS submission (the plugin itself ships English only).

## Day-to-day
1. Edit under `workspace/format_multitopic/`.
2. If you touch `amd/src/*.js`, rebuild with `grunt amd` (rollup) against a Moodle checkout and commit
   the regenerated `amd/build/*`. Never hand-edit `amd/build/`.
3. Run the `moodle-plugin-ci` checks listed in [`CONTRIBUTING.md`](../CONTRIBUTING.md).
4. Bump `$plugin->version` / `$plugin->release` in `version.php` for anything Moodle must detect as an
   update, and add a `CHANGELOG.md` entry.

## Packaging & deploy
- `./scripts/package_workspace.sh` stages `workspace/format_multitopic/` as `multitopic/` (the bare
  folder name Moodle expects inside `course/format/`) and writes a timestamped ZIP to `build/`.
- Install the ZIP via **Site administration → Plugins → Install plugins**, or copy the folder to
  `course/format/multitopic` and run the upgrade.

## Upstream sync
- `upstream` remote = `git@github.com:james-cnz/moodle-format_multitopic.git`.
- Because the plugin now lives under `workspace/format_multitopic/`, pull upstream changes with a
  path-aware diff (e.g. `git diff upstream/master -- <paths>`) or `git subtree`, not a plain merge.
