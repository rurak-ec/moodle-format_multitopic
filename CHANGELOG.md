# Changelog

All notable changes to the rurak-ec fork of **format_multitopic** are documented here.
This fork tracks [james-cnz/moodle-format_multitopic](https://github.com/james-cnz/moodle-format_multitopic)
upstream; entries below describe the fork-specific deltas. The format is based on
[Keep a Changelog](https://keepachangelog.com/).

## [Unreleased]

### Changed
- Forked from upstream `v5.1.1` and adopted the rurak-ec workspace repo layout
  (`workspace/format_multitopic/`, `scripts/`, `docs/`, root `README`/`CONTRIBUTING`/`LICENSE`,
  `.github/workflows/moodle-plugin-ci.yml`).
- Capped the supported Moodle range to **5.0 – 5.3-dev** via `$plugin->supported = [500, 503]`
  (CI matrix: 5.0 / 5.1 / 5.2 stable + non-blocking `main` / 5.3-dev).

### Code quality (no functional changes)
- Replaced the hand-maintained 101-entry bannerslice percentage list with an equivalent generated
  array (`array_map(... str_pad ..., range(0, 100))`); proven byte-identical to the original.
- Removed factually-incorrect `@since Moodle 2.0` / `@since Moodle 2.3` file-header tags.
- Added `phpcs.xml.dist` (Moodle standard with the `moodle.Commenting.TodoComment` issue-reference
  requirement relaxed for upstream fork notes), so `phpcs --max-warnings 0` is clean (0 errors,
  0 warnings) — verified locally with `moodlehq/moodle-cs`.

### Deferred (next, CI-gated)
- Behaviour-preserving refactors that need the Behat suite as a safety net are **not** done yet:
  splitting `fmt_get_sections_extra()` into forward/reverse helpers, de-duplicating the
  `course_format_options()` / `section_format_options()` form builders, and regenerating
  `amd/build/` via `grunt amd`. These will land once `moodle-plugin-ci behat` runs them green in CI.
- Moodle 4.5 support is intentionally **not** declared; lowering the floor below 5.0 requires
  compatibility shims and is deferred to a separate functional pass.
