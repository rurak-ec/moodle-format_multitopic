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
- Split the 178-line `fmt_get_sections_extra()` into `fmt_sections_extra_forward_pass()` and
  `fmt_sections_extra_reverse_pass()` private helpers. Verified behaviour-preserving: the computed
  section-extra output (sanitised levels, prev/next/parent links, visibility, date range,
  current-nesting, page depth) is byte-identical on a multi-level course with periods and hidden
  sections; phpcs 0/0. The internal logic was moved verbatim (de-indented), not rewritten.
- Replaced the hand-maintained 101-entry bannerslice percentage list with an equivalent generated
  array (`array_map(... str_pad ..., range(0, 100))`); proven byte-identical to the original.
- Removed factually-incorrect `@since Moodle 2.0` / `@since Moodle 2.3` file-header tags.
- Added `phpcs.xml.dist` (Moodle standard with the `moodle.Commenting.TodoComment` issue-reference
  requirement relaxed for upstream fork notes), so `phpcs --max-warnings 0` is clean (0 errors,
  0 warnings) — verified locally with `moodlehq/moodle-cs`.

### Reviewed but not changed
- `course_format_options()` / `section_format_options()` were **not** merged: they only share
  Moodle's idiomatic static-cache skeleton; their option sets differ entirely (the `hiddensections`
  version-branch exists once, in `course_format_options()`), so there is no real duplication to
  extract and merging would break the expected per-method pattern.

### Deferred (next, CI-gated)
- De-duplicating the `($section->section == 0 || $section->uservisible [&& is_section_visible])`
  visibility predicate that recurs across ~6 files into a shared helper — a real but broad change,
  kept for its own focused commit (precedence-sensitive; touches many output classes).
- Moodle 4.5 support is intentionally **not** declared; lowering the floor below 5.0 requires
  compatibility shims and is deferred to a separate functional pass.
